<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Staff;
use App\Models\Investor;
use App\Models\Affiliate;
use App\Structures\BirthdayPerson;
use App\Notifications\Shared\BirthdayMessageNotification;


class BirthdayService
{
    
    /**
     * All models we want to send birthday messages
     *
     * @var array
     */
    protected $birthdayModels = [
        'borrower'=> User::class,
        'affiliate'=> Affiliate::class,
        'investor'=> Investor::class,
        'staff'=> Staff::class
    ];
    
    /**
     * Get All Customer Birthdays Current day
     *
     * @return array
     */
    public function birthdaysToday()
    {
        $today = Carbon::today();

        $day = $today->day;

        $month  = $today->month;

        $birthdays = $this->getBirthdayQuery($day, $month);

        return $birthdays;
    }

    
    /**
     * Get All customer birthdays the next day
     *
     * @return array
     */
    public function birthdaysTomorrow()
    {
        $tomorrow = Carbon::tomorrow();

        $day = $tomorrow->day;

        $month = $tomorrow->month;

        $birthdays = $this->getBirthdayQuery($day, $month);

        return $birthdays;
    }

    
    /**
     * Search through for birthdays
     * 
     * @param int $day
     * @param int $month
     * @param string $type
     * @return array
     */
    public function searchBirthday($day, $month, $model)
    {
        return $this->getBirthdayQuery($day, $month, $model);
    }
    
    /**
     * Querys the models based on supplied dates
     *
     * @param  int $month
     * @param  int $day
     * @return array
     */
    protected function getBirthdayQuery($day, $month, $model = null) 
    {
        if (is_null($model) || !isset($model) ) {
            // No model was passed so we search all the birthday models

            $birthdays = collect([]);

            $models = array_values($this->birthdayModels);

            foreach ($models as $model) {

                $queryResult = $this->queryModelBirthday($day, $month, $model);
            
                $birthdays = $birthdays->merge($queryResult);
                
                //array_push($birthdays, $queryResult);
            }

        } else {

            $model = $this->birthdayModels[$model];

            $birthdays = $this->queryModelBirthday($day, $month, $model);
        }

       

        return $birthdays;
    }

    
    /**
     * Underlining query for getting birthdays
     *
     * @param  int $day
     * @param  int $month
     * @param  string $model
     * @return void
     */
    protected function queryModelBirthday($day, $month, $model) 
    {
        if ($model !== 'App\Models\User') { // this code just skips other models
            return collect([]);                      // because only the users table has dob column
        }

        $query = new $model;

        if (isset($day)) {
            $query = $query->whereDay('dob', $day);
        }

        if (isset($month)) {

            $query = $query->whereMonth('dob', $month);
        }

        return $query->get();
    }

    
    /**
     * Sends notifications for birthdays that are due
     *
     * @return void
     */
    public function sendNotificationToday()
    {
        $birthdays = $this->birthdaysToday();

        foreach ($birthdays as $birthday) {

            $birthdayPerson = new BirthdayPerson($birthday);

            $birthdayPerson->user()->notify(new BirthdayMessageNotification());
        }

    }
}