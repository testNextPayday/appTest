<?php
namespace App\Services\Investor;

use PDF;
use Carbon\Carbon;
use NumberFormatter;
use App\Models\Staff;
use App\Models\Investor;
use App\Models\Affiliate;
use App\Models\BankDetail;
use App\Mail\InvestmentMade;
use Illuminate\Http\Request;
use App\Models\PromissoryNote;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\InvestmentCertificate;
use App\Unicredit\Collection\Utilities;
use App\Events\PromissoryNoteCreatedEvent;
use App\Services\Investor\PromissoryNoteSettingsUpdate;
use Illuminate\Support\Facades\Http;

class PromissoryNoteService

{

    use PromissoryNoteSettingsUpdate;
    
    /**
     * Formerly createPromissoryNote 
     * @desc Used in creating the first batch of notes
     * @deprecated 
     * 
     * @param  mixed $request
     * @param  mixed $investor
     * @return void
     */
    public function createPromissoryNote9(Request $request, Investor $investor)
    {

        $this->checkStartDate();
            
        $data = $request->all();
     
        // Generate PDF for certificate
        $neededPDFData = $request->only(['start_date', 'tenure', 'rate', 'amount', 'tax', 'interest_payment_cycle']);
        $neededPDFData['name'] = $investor->name;
        $pdfData = $this->generatePDFData($neededPDFData);

        // Generate certificate for note
        $neededCertData = $pdfData;
        $certData = $this->generateCertData($neededCertData);

        // Create the note for investor
        $interestAmount = (($data['amount'] * ($data['rate']/100)) / 12) * $data['tenure'];
        $promissoryNote = $this->generateNoteData($data);
        $promissoryNote['investor_id'] = $investor->id;
        $promissoryNote['maturity_date'] = $pdfData['maturity_date'];
        $promissoryNote['maturity_value'] = $pdfData['maturity_value'];
        $promissoryNote['payable_value'] = $pdfData['payable_value'];
        $promissoryNote['interest'] = $interestAmount - $pdfData['tax_amount'];
        

        $certificate = InvestmentCertificate::create($certData);

        if ($certificate) {

            $note = PromissoryNote::create($promissoryNote);

            // attach certificate to note
            $certificate->update(['promissory_note_id'=> $note->id]);

            if ($note->isUpfront()) {

                $note->service->upfrontInterest();
            }
            

            if ($investor->email && isset($data['send_email'])) {
                $certificateLink = $pdfData['certificateUrl'];
                Mail::to($investor->email)->send(new InvestmentMade($certificateLink)); 
            }

            if (isset($request->receiverType)) {

                $model = $this->determineReceivingModel($request->receiverType);

                $model->find($request->assignedPersonId);

                $investor->update(['adder_type'=> get_class($model), 'adder_id'=>$model->id]);

                $noteCreatedEvtData = $request->only(['receiverType', 'assignedPersonId', 'tenure', 'amount']);
                $noteCreatedEvtData['note'] = $certificate;
                event(new PromissoryNoteCreatedEvent($noteCreatedEvtData));
            }

            return true;

           
        }

        return false;
            
    }
    
    /**
     * Creates a promissory Note
     *
     * @param  mixed $request
     * @param  mixed $investor
     * @return void
     */
    public function createPromissoryNote(Request $request, Investor $investor)
    {

        $promissoryNote = $this->generateNoteData($data = $request->all());

        $loggedInUser = Utilities::currentlyAuthUser();

        $promissoryNote['investor_id'] = $investor->id;
        $promissoryNote['status'] = 0; // status is set at pending
        $promissoryNote['adder_id']  = $loggedInUser->id;
        $promissoryNote['adder_type'] = get_class($loggedInUser);
       
        if ($created = PromissoryNote::create($promissoryNote)) {

            return true;
        }

        return false;

    }

    public function createPromissoryNote2($data, Investor $investor)
    {

        $promissoryNote = $this->generateNoteData2($data);

        $loggedInUser = Utilities::currentlyAuthUser();

        $promissoryNote['investor_id'] = $investor->id;
        $promissoryNote['status'] = 0; // status is set at pending
        $promissoryNote['adder_id']  = $loggedInUser->id;
        $promissoryNote['adder_type'] = get_class($loggedInUser);
        Log::info($promissoryNote);
        if ($created = PromissoryNote::create($promissoryNote)) {

            return true;
        }

        return false;

    }

    
    /**
     * Check we can delete a note
     *
     * @param  mixed $promissoryNote
     * @return void
     */
    protected function checkDelete(PromissoryNote $promissoryNote)
    {
        if ($promissoryNote->status >= 1) {
            throw new \Exception('We cannot delete a note that is no longer pending');
        }

        return true;
    }

    
    /**
     * Check that we can update a note
     *
     * @param  mixed $promissoryNote
     * @return void
     */
    protected function checkUpdate(PromissoryNote $promissoryNote)
    {
        if ($promissoryNote->status >= 1) {
            throw new \Exception('We cannot update a note that is no longer pending');
        }

        return true;
    }

    
    /**
     * Deletes a promissory note
     *
     * @param  mixed $promissoryNote
     * @return void
     */
    public function delete(PromissoryNote $promissoryNote)
    {

        if ($this->checkDelete($promissoryNote)) {

            $promissoryNote->delete();
        }
    }

    
    /**
     * Update the note 
     *
     * @param  mixed $promissoryNote
     * @param  mixed $data
     * @return void
     */
    public function update(PromissoryNote $promissoryNote, $data)
    {

        if ($this->checkUpdate($promissoryNote)) {

            $promissoryNote->update($data = $this->generateNoteData($data));
        }
    }

    
    /**
     * Carries out this task ones there is an approval
     *
     * @param  mixed $note
     * @return void
     */
    public function performApprovalTaskLineUp(Request $request, $note)
    {

        $investor = $note->investor;
        

        // Generate PDF
        $neededPDFData = [
            'start_date'=> $note->start_date,
            'tenure'=> $note->tenure,
            'amount'=> $note->principal,
            'rate'=> $note->rate,
            'tax'=> $note->tax,
            'interest_payment_cycle'=> $note->interest_payment_cycle,
            'name'=> $note->investor->name,
            'maturity_date'=> $note->maturity_date,
            'maturity_value'=> $note->maturity_value
        ];
        
        $leftOverPDF = $this->createPDF($neededPDFData);
        
        // Generate Certificate Data
        $certificateData = [
            'name'=> $note->investor->name,
            'start_date'=> $note->start_date,
            'maturity_date'=> $note->maturity_date,
            'amount'=> $note->principal,
            'rate'=> $note->rate,
            'interest_payment_cycle'=> $note->interest_payment_cycle,
            'certificate'=> $leftOverPDF['certificateLink'],
            'promissory_note_id'=> $note->id,
            'reference'=> $leftOverPDF['reference']
        ];
        

        $certificate  = InvestmentCertificate::create($certificateData);

        //Check for upfront and initialize withdrawal
        if ($note->isUpfront()) {
            $note->service->upfrontInterest();
        }

        //Check form emails and send
        if ($note->investor->email && $request->send_email == 'on') {
            
            $certificateLink = $leftOverPDF['certificateUrl'];
            Mail::to($investor->email)->send(new InvestmentMade($certificateLink)); 
        }

        // Pay commission
        $this->payCommission($request, $investor, $note);

        // Update note to active
        $note->update(['status'=> 1]);

        
    }

    
    /**
     * Pays the referer commission
     *
     * @param  mixed $request
     * @param  mixed $investor
     * @param  mixed $certificate
     * @return void
     */
    protected function payCommission(Request $request, $investor, $note)
    {
        if (isset($request->receiverType)) {

            $model = $this->determineReceivingModel($request->receiverType);

            $model = $model->find($request->assignedPersonId);

            $investor->update(['adder_type'=> get_class($model), 'adder_id'=>$model->id]);

            $noteCreatedEvtData = $request->only(['receiverType', 'assignedPersonId']);
            $noteCreatedEvtData['note'] = $note;
            $noteCreatedEvtData['amount'] = $note->principal;
            $noteCreatedEvtData['tenure'] = $note->tenure;
            event(new PromissoryNoteCreatedEvent($noteCreatedEvtData));
        }
    }

    
    /**
     * Creates PDF From promissory note data
     *
     * @param  mixed $data
     * @return void
     */
    protected function createPDF($data)
    {
       
        $certificate = new InvestmentCertificate();
        $referenceNumber = $certificate->generateReference();

        $data['reference'] = $referenceNumber;
        
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        
        $data['amount_in_words'] = $f->format($data['amount']);
        $data['tax_amount'] = $data['tax'];
        $data['maturity_date'] = Carbon::parse($data['maturity_date']);
        $data['start_date'] = Carbon::parse($data['start_date']);
        
        $data['monthly_interest'] = ($this->getInterestAmount($data) - $this->getTaxAmount($data)) / $data['tenure'];
        
        $pdf_name = $referenceNumber . '.pdf';
        $pdf = PDF::loadView('pdfs.investment_certificate', $data);
        $storePath = 'pdfs/certificates/' .$pdf_name;
        $certificateLink = public_path() . '/storage/' . $storePath;
        $pdf->save($certificateLink);

        $data['certificateUrl'] = $certificateLink;
        $data['certificateLink'] = $storePath;

        return $data;

    }

    
    /**
     * Calculate total amount paying as tax
     *
     * @param  mixed $data
     * @return void
     */
    private function getTaxAmount($data)

    {
        return ($data['tax'] / 100 ) * $this->getInterestAmount($data);
    }

    
    /**
     * Calculate interest 
     *
     * @param  mixed $data
     * @return void
     */
    private function getInterestAmount($data)
    {
        $amount  = $data['amount'];
        $rate = $data['rate'];
        $tenure = $data['tenure'];
        $interestAmount = (($amount * ($rate/100)) / 12) * $tenure;

        return $interestAmount;
    }
    
    /**
     * Calculate the maturity date
     *
     * @param  mixed $data
     * @return void
     */
    private function getMaturityDate($data)
    {
        $maturity_date = Carbon::parse($data['start_date'])->addMonths($data['tenure'])->endOfMonth();

        return $maturity_date->toDateString();
    }
    
    /**
     * Calculate the maturity value of note
     *
     * @param  mixed $data
     * @return void
     */
    private function getMaturityValue($data)
    {
        if (strtolower($data['interest_payment_cycle']) == 'backend') {

            $value = $data['maturity_value'] = $data['amount'] + ($this->getInterestAmount($data) - $this->getTaxAmount($data));

        } else {

            $value = $data['amount'];
        }

        return $value;
    }



    private function getPayableValue($data)
    {

    }


    
    /**
     * Checks the start date of the loan to ensure it works
     *
     * @param  mixed $request
     * @return void
     */
    public function getStartDate($data)
    {

        $startDate = Carbon::parse($data['start_date']);

        if ($startDate->day > 28) {

            $startDate->day = 1;

            $startDate->addMonth();

            $date = $startDate->toDateString();

            return $date;
        }

        return $startDate->toDateString();


    }

    
    /**
     * determineReceivingModel
     *
     * @param  mixed $receiver
     * @return void
     */
    protected function determineReceivingModel($receiver)
    {
       
        switch($receiver) {
            case 'affiliate':
                $model = new Affiliate;
            break;

            case 'staff':
                $model = new Staff;
            break;

            case 'investor':
                $model = new Investor;
            break;

            default:
                throw new \InvalidArgumentException('No model found');
        }

        return $model;
    }

    
    
    /**
     * Generates data needed to create promissory notes
     *
     * @param  mixed $data
     * @return void
     */
    protected function generateNoteData($data)
    {


        $promissoryNote = [
            'interest_payment_cycle'=> $data['interest_payment_cycle'],
            'start_date'=> $this->getStartDate($data),
            'principal'=> $data['amount'],
            'rate'=> $data['rate'],
            'tenure'=> $data['tenure'],
            'current_value'=> $data['amount'],
            'monthsLeft'=> $data['tenure'],
            'previous_note_id'=> @$data['previous_note_id'] ?? null,
            'maturity_value'=> $this->getMaturityValue($data),
            'maturity_date'=> $this->getMaturityDate($data),
            'payable_value'=> $data['amount'],
            'interest'=> $this->getInterestAmount($data) - $this->getTaxAmount($data),
            'tax'=> $data['tax'],
            
        ];

        return $promissoryNote;

    }

    protected function generateNoteData2($data)
    {


        $promissoryNote = [
            'interest_payment_cycle'=> $data['interest_payment_cycle'],
            'start_date'=> $this->getStartDate($data),
            'principal'=> $data['amount'],
            'rate'=> $data['rate'],
            'tenure'=> $data['tenure'],
            'current_value'=> $data['amount'],
            'monthsLeft'=> $data['tenure'],
            'previous_note_id'=> @$data['previous_note_id'] ?? null,
            'maturity_value'=> $this->getMaturityValue($data),
            'maturity_date'=> $this->getMaturityDate($data),
            'payable_value'=> $data['amount'],
            'interest'=> $this->getInterestAmount($data) - $this->getTaxAmount($data),
            'tax'=> $data['tax'],
            'payment_type'=> $data['payment_type'],
        ];

        return $promissoryNote;

    }

    
    /**
     * Generates Certificate creation data
     * 
     * Kept here for the purpose of easily adding new possibilities
     *
     * @param  mixed $data
     * @return void
     */
    protected function generateCertData($data)
    {
        unset($data['tenure']);
        unset($data['amount_in_words']);
        unset($data['maturity_value']);
        unset($data['tax_amount']);
        unset($data['payable_value']);
        $data['certificate'] = $data['certificateLink'];
        unset($data['certificateLink']);
        unset($data['certificateUrl']);
        return $data;
    }
    
    /**
     * Generates data for certificate pdf
     *
     * @param  mixed $data
     * @return void
     */
    protected function generatePDFData($data)
    {
        $certificate = new InvestmentCertificate();
        $reference = $certificate->generateReference();

        $data['reference'] = $reference;
        $start_date = Carbon::parse($data['start_date']);
        
        $data['start_date'] = $start_date;
        $maturity_date = Carbon::parse($data['start_date'])->addMonths($data['tenure'])->endOfMonth();
        $data['maturity_date'] = $maturity_date;
        
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $data['amount_in_words'] = $f->format($data['amount']);
        $interestAmount = (($data['amount'] * ($data['rate']/100)) / 12) * $data['tenure'] ;
        $data['maturity_value'] = $data['amount'];
        $data['tax_amount'] = ($data['tax']/100) * $interestAmount;
        $data['payable_value'] = $data['amount'];
        $data['interest'] = $interestAmount;

        if (strtolower($data['interest_payment_cycle']) == 'backend') {
            $data['maturity_value'] = $data['amount'] + ($interestAmount - $data['tax_amount']);
        }

        $data['monthly_interest'] = ($interestAmount - $data['tax_amount'])/ $data['tenure'];
 
        $pdf_name = $reference . '.pdf';

        $pdf = PDF::loadView('pdfs.investment_certificate', $data);
        $storePath = 'pdfs/certificates/' .$pdf_name;
        $certificateLink = public_path() . '/storage/' . $storePath;
        $pdf->save($certificateLink);
        
        $data['certificate'] = 'public/' . $storePath;

        $data['start_date'] = $start_date->toDateString();
        $data['maturity_date'] = $maturity_date->toDateString();
        $data['certificateUrl'] = $certificateLink;
        $data['certificateLink'] = $storePath;

        //avoiding trouble
        unset($data['interest']);
        unset($data['monthly_interest']);

        return $data;
    }

    
    protected function sendFBNQuestLetter($note)
    {

    }


    
    /**
     * Create bank details
     *
     * @param  mixed $investor
     * @param  mixed $data
     * @return void
     */
    public function createBankDetails($investor, $data)
    {
        $getBanks =  Http::get('https://api.paystack.co/bank');
        $banks = json_decode($getBanks);


        $code = $data['bank_code'];

        $bank = null;



        foreach ($banks->data as $getBank) {
            if ($getBank->code == $code) {
                $bank = $getBank->name;
            }
        }
        


        $accountNumber = $data['account_number'];

        $investor->banks()->each(function($bank) {
            $bank->delete();
        });

        $bankData = [
            'bank_code'=> $code,
            'account_number'=> $accountNumber,
            'bank_name'=> $bank
        ];

        $details  = $investor->banks()->create($bankData);

        return $details ? true : false;
    }







}