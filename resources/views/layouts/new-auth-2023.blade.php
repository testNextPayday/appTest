<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next Payday App</title>
    <meta name="description" content="Next Payday is an alternative finance company focused at providing payday loans to validly employed Nigerians through a network of investors."/>
    <meta name="keywords" content="Loan, Investment, Salary, Salary Advance, Finance" />
    <meta name="author" content="Next Payday" />
    <meta name="robots" content="index,follow">
    <link rel="shortcut icon" href="{{ asset('new_auth_assets/img/favicon.png') }}" type="image/x-icon">

    <!-- CSS here -->
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/bootstrap.min.css') }}" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/elegant-icons.min.css') }}" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/all.min.css') }}" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/animate.css') }}" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/nice-select.css') }}" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/slick.css') }}" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/slick-theme.css') }}" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/default.css') }}" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/style.css') }}" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_auth_assets/css/responsive.css') }}" media="all" />
</head>

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div id="ctn-preloader2" class="ctn-preloader2">
            <img src="{{ asset('new_auth_assets/img/logo/preloader.gif') }}">
        </div>
    </div>
    <!-- Header -->
    <header class="header">
        <div class="header-menu header-menu-2 bg_white" id="sticky">
            <nav class="navbar navbar-expand-lg ">
                <div class="container">
                    <a class="navbar-brand sticky_logo" href="home.html">
                        <img class="main" src="{{ asset('new_auth_assets/img/logo/logo-1.png') }}" alt="logo">
                        <img class="sticky" src="{{ asset('new_auth_assets/img/logo/logo-1.png') }}" alt="logo">
                    </a>
                    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="menu_toggle">
                            <span class="hamburger">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                            <span class="hamburger-cross">
                                <span></span>
                                <span></span>
                            </span>
                        </span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav menu ms-auto">
                            <li class="nav-item dropdown submenu ">
                                <a class="nav-link" href="home.html">Home</a>
                            </li>
                            <li class="nav-item dropdown submenu">
                                <a class="nav-link" href="about.html">About Us</a>
                            </li>
                            <li class="nav-item dropdown submenu">
                                <a class="nav-link" href="faq.html">FAQS</a>
                            </li>
                            <li class="nav-item dropdown submenu">
                                <a class="nav-link" href="https://medium.com/@nextpayday" target="_blank">Blog</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!-- Header end-->

    <main>
        <!-- Persinal Details start -->
        <section class="pt-190 pb-100 get-touch-area bg_white">
            <div class="container">
                <div class="row gy-4 gy-lg-0">
                    <div class="col-lg-5">
                        <div class="section-title text-start">
                            <h2>Account Login</h2>
                            <p>Welcome back. Enter your registered phone number and password to access your dashboard</p>
                            <div class="text-center pt-10">
                                <div class="row gy-md-0 gy-4 align-items-center h-100">
                                    <div class="col-md-5 col-6">
                                        <img class="img-fluid wow fadeInRight" data-wow-delay="0.1s"
                                            src="{{ asset('new_auth_assets/img/verified/ndpr2.png') }}" alt="NPDR"></a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 offset-lg-1">
                        <div class="contact-form-widget">
                            {{-- <form action="#login" method="POST">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label for="f-name">Phone Number</label>
                                        <input type="text" id="f-name" name="name" class="form-control"
                                            placeholder="Enter Phone number" required>
                                    </div>
                                    <div class="col-md-10 mt-20">
                                        <label for="form-sub">Password</label>
                                        <input type="password" id="form-sub" name="password" class="form-control"
                                            placeholder="Enter password" required>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <p class="policy-text">Forgot Password?
                                            <a class="option-text" href="forgot-password.html"><span>Reset it</span></a>
                                        </p>
                                    </div>
                                    <div class="col-10 mt-30">
                                        <button type="submit" class="theme-btn theme-btn-lg w-100">Login</button>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <p class="policy-text">Don't have an account?
                                            <a class="option-text" href="signup.html"><span>Create an Account</span></a>
                                        </p>
                                    </div>
                                </div>
                            </form> --}}

                            @yield('auth_content')
                        </div>
                    </div>
                </div>
            </div>
        </section>

<!-- footer -->
<footer class="footer footer-2 pt-lg-120 pt-110 pb-100 pb-lg-125"
style="background-image: url(img/footer/footer-bg-2.png);">
<div class="footer-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-sm-6 text-center text-sm-start">
                <div class="footer-widget mb-30 wow fadeInLeft">
                    <div class="footer-text">
                        <p><a href="home.html" class="p-0 m-0"><img src="{{ asset('new_auth_assets/img/logo/logofooter.png') }}" alt="logo"></a>
                        </p>
                        <br>
                        <p><a href="https://goo.gl/maps/en9G8hxHYjtGWGY96" target="_blank">11A Igbodo Street, Old GRA, Port Harcourt</a>
                        <p>+234 (0) 809 500 0667
                        </p>
                        <div class="copyright pt-15">
                            <div class="container">
                                <div class="row ">
                                    <div class="social-button">
                                        <a href="https://www.linkedin.com/company/nextpaydayng/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                        <a href="https://www.instagram.com/nextpaydayng/"target="_blank"><i class="fab fa-instagram"></i></a>
                                        <a href="https://www.x.com/nextpaydayng/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
                                        <a href="https://www.facebook.com/nextpaydayng/" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 text-center text-sm-start">
                <div class="footer-widget mb-30 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="f-widget-title">
                        <h5>Quick Links</h5>
                    </div>
                    <div class="footer-link">
                        <ul>
                            <li><a href="login.html#">Borrowers</a></li>
                            <li><a href="investor.html">Investors</a></li>
                            <li><a href="https://app.nextpayday.co/affiliates/login" target="_blank">Affiliates</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 text-center text-sm-start">
                <div class="footer-widget mb-30 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="f-widget-title">
                        <h5>Company</h5>
                    </div>
                    <div class="footer-link">
                        <ul>
                            <li><a href="about.html">About Us</a></li>
                            <li><a href="https://medium.com/@nextpayday" target="_blank">Blog</a></li>
                            <li><a href="faq.html">FAQS</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-sm-6 text-center text-sm-start">
                <div class="footer-widget mb-30 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="f-widget-title">
                        <h5>Legal</h5>
                    </div>
                    <div class="footer-link">
                        <ul>
                            <li><a data-bs-toggle ="modal" data-bs-target="#applyModalTerms">
                                Terms of Service</a></li>
                            <li><a data-bs-toggle ="modal" data-bs-target="#applyModalPrivacy">
                                Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal: Terms of Service -->
<div class="modal terms-modal fade" id="applyModalTerms" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4>TERMS AND CONDITIONS:
                    <br>DEFINITIONS</h4>
                    <br><strong>Whereas:</strong>
                    <br>1. The Borrower has applied for Next Payday Personal Installment Loan to meet urgent personal cash flow needs
                    <br>2. Next Payday has agreed to grant the Personal Installment Loan to the Borrower by way of principal outstanding
                    <br>3. The Loan amount shall be made available by direct payment into borrower's current or saving account held with any Nigerian Bank
                    <p>
                        <br><strong>It is agreed as follows:</strong>
                        <br>
                        <strong>1. The Loan</strong>
                        <br>Next Payday hereby agrees to make available to the Borrower and the Borrower accepts the sum to be disbursed (hereinafter referred to as “the Personal Installment Loan”), by Next Payday after borrower’s risk assessment and scoring, for a period not exceeding 90 Days from the date of disbursement.
                        <p>
                            <br><strong>2. Interest Rate</strong>
                            <br>2.1 There will be a risk - based interest rate of 5%-10% per disbursal which will be taken upfront or capitalised which translates to annual percentage rate of 60%-120%. There shall not be any refund on interest rate collected upfront for any prepayment, pre termination of the loan before 30day period or for any other reason. If the Borrower fails to pay any amount which the Borrower owes Next Payday in terms of this agreement on the due date, Next Payday shall be entitled to continue to charge interest on the outstanding amount at the same rate until full repayment
                            <br>2.2 There shall be a non-refundable loan Fee of 2.5% (taken upfront or capitalised) of the disbursed loan amount. Loan fee is made up of credit life insurance, management fee and other API charges associated with the loan
                            <br>
                            <br><strong>3. Payment</strong>
                            <br>3.1 The Borrower agrees that Next Payday shall have the right to deduct the due repayment in full either directly from any of the borrower’s accounts or from the salary payment source including salary account, saving account, deposit account, corporate account with any bank in Nigeria or outside of Nigeria including all accounts linked to the Borrowers Bank Verification Number (BVN)
                            <br>3.2 The Borrower hereby gives Next Payday the right to deduct monies owing to it from any credit inflow and salary inflow into the accounts in any Bank or payment platform in Nigeria and outside Nigeria
                            <br>
                            <br><strong>4. Costs and Charges</strong>
                            <br>All out-of-pocket expenses including registration, legal fees, stamp duties and other fees incurred by Next Payday in processing of this facility including enforcement of security and recovery of facility in the event of default shall be for the account of the Borrower.
                            <br>
                            <br><strong>5. Breach</strong>
                            <br>In the event of:
                            <br>i. any failure by the Borrower to pay any amount which is due and outstanding under this agreement
                            <br>ii. any breach by the Borrower of the terms of this agreement or,
                            <br>iii. any failure by the Borrower to carry out his or her obligations under this agreement; then the full sum outstanding under this agreement, together with any penal charge (if any) and all other charges and expenses owing to and due to Next Payday by the Borrower shall become immediately due and payable, and without giving notice to the Borrower, Next Payday shall be entitled to terminate this agreement and claim and/or recover from the Borrower any damages/losses it may have suffered as a consequence
                            <br>
                            <br><strong>6. Authorization to comply</strong>
                            <br>The Borrower agrees that Next Payday is irrevocably authorized to comply with any instructions on the Service receives on his/her behalf through Next Payday Channels and it is agreed that such Instruction shall be irrevocably deemed to be the Borrower’s Instruction.
                            <br>
                            <br><strong>7. Notices</strong>
                            <br><strong>7.1 Set off and consolidation rights</strong>
                            <br>Next Payday may at any time and without notice to the Borrower combine all or any of the Borrower’s accounts and liabilities with Next Payday whether singly or jointly with any person, or set off all or any monies standing to the credit of such account(s) including the Borrower’s deposits with Next Payday (whether matured or not) towards satisfaction of any of the Borrower’s liabilities to Next Payday whether as principal or surety, actual or contingent, primary or collateral, singly or jointly with any other person.
                            <br>
                            <br>By accepting the terms & conditions of the loan and by drawing on the loan. I covenant to repay the loan as and when due. In the event that I fail to repay the loan as agreed, and the loan becomes delinquent, Next Payday shall have the right to report the delinquent loan to the CBN through the Credit Risk Management System (CRMS) or by any other means, and request the CBN to exercise its regulatory power to direct all banks and other financial institutions under its regulatory purview to set-off my indebtedness from any money standing to my credit in any bank account and from any other financial assets they may be holding for my benefit.
                            <br>I covenant and warrant that the CBN shall have power to set-off my indebtedness under this loan agreement from all such monies and funds standing to my credit/benefit in any and all such accounts or from any other financial assets belonging to me and in the custody of any such bank.
                            <br>I hereby waive any right of confidentiality whether arising under common law or statute or in any other manner whatsoever and irrevocably agree that I shall not argue to the contrary before any court of law, tribunal, administrative authority or any other body acting in any judicial or quasi-judicial capacity.
                            <br>
                            <br><strong>7.2 Universal consolidation rights</strong>
                            <br>The Borrower covenants that Nextpayday shall have the right to use the Borrower's Bank Verification Number (BVN) and without recourse to the Borrower, to recover the delinquent amount from all accounts linked to the Borrower's BVN in all banks and other financial institutions using third-party solutions.
                            <br>
                            <br><strong>8. Authorization to comply</strong>
                            <br>The Borrower agrees that Next Payday is irrevocably authorized to comply with any instructions on the Service receives on his/her behalf through Next Payday channel and it is agreed that such Instruction shall be irrevocably deemed to be the Borrower’s instruction.
                            <br>
                            <br><strong>9. Notices</strong>
                            <br>9.1 The Borrower agrees to accept service of all notices, processes and any other communication relating to this loan through email address and/ or SMS to phone number registered with Next Payday and hereby confirms these phone numbers and addresses as his/ her phone number and address for service. Therefore, the Borrower also agrees that it is his/her responsibility to ensure that his/her contact details including phone number maintained with Next Payday are valid
                            <br>9.2 All notices and processes sent by registered post will be deemed to have been received Seven (7) days after the date of posting; all notices and processes delivered by hand shall be deemed to have been received on the day such letter is dated
                            <br>
                            <br><strong>10. Appropriation</strong>
                            <br>10.1 All amounts received by Next Payday will be first apportioned towards overdue interest charged /fees. Any balance outstanding thereafter shall be appropriated lastly towards the principal sum
                            <br>10.2 Next Payday reserves the right to refuse acceptance of post-dated cheques or such other instruments towards payment or settlement of the credit facility
                            <br>
                            <br><strong>11. Indemnity</strong>
                            <br>The Borrower agrees to fully indemnify Next Payday against all costs and expenses (including legal fees, collection commission et cetera) arising in any way in connection with the Borrower’s accounts, these terms and conditions, in enforcing these terms and conditions or in recovering any amounts due to Next Payday or incurred by Next Payday in any legal proceedings of whatever nature.
                            <br>
                            <br><strong>12. Waiver</strong>
                            <br>12.1 No forbearance, neglect or waiver by Next Payday in the enforcement of any of these terms and conditions shall prejudice Next Payday’s right to strictly enforce the same. No waiver by Next Payday shall be effective unless it is in writing
                            <br>12.2 In so far as any right is conferred on the Borrower with regard to any obligation imposed on the Borrower by this contract, the Borrower hereby waives and forgoes all such rights and benefits, whether conferred by a statute
                            <br>
                            <br>13. Next Payday hereby gives Notice to the Borrower of its duty to share information on the Borrower’s credit status and business history as may be required from time to time by Regulators.
                            <br>
                            <br><strong>14. Assignment to Third Parties</strong>
                            <br>Next Payday reserves the right to assign this agreement to a third party without the permission of the Borrower.
                            <br>
                            <br><strong>15. Service Availability</strong>
                            <br>15.1 Use of the Service may from time to time be unavailable, delayed, limited or slow due to, but not restricted to the following factors:
                            <br>•	Force majeure
                            <br>•	Industrial Strike Actions
                            <br>•	Failure from hardware and software
                            <br>•	System capacities overload
                            <br>•	Failure of or suspension of public or private telecommunication networks
                            <br>•	Power supply or other utilities interruption
                            <br>•	Government or regulatory restrictions, court or tribunal rulings, amendment of legislation or other human intervention
                            <br>•	Any other cause whatsoever beyond Next Payday’s control
                            <br>15.2 The Borrower acknowledges and agrees that internet and telecommunications transmissions are never completely private or secured
                            <br>15.3 All content and services on or available through the Services are provided on an "as is" basis and Next Payday does not make any representation or give any warranty or guarantee in respect of the Service or its content
                            <br>15.4 Next Payday may discontinue or make changes in the Service at any time without prior notice to the Borrower and without any liability whatsoever
                            <br>
                            <br><strong>16. General Provisions and Conditions</strong>
                            <br>16.1 Drawdown under the facility is subject to availability of funds
                            <br>16.2 SMS alerts / notification charges incurred in relation to this loan shall be for the account of the borrower
                            <br>16.3 The Borrower irrevocably undertakes that for the period of this agreement, he or she will maintain his or her bank account designated for the purposes of the loan with Next Payday
                            <br>16.4 In the event that the facility becomes due and unpaid, Next Payday reserves the right to notify Embassies, High Commissions, foreign consulates, referees, other relevant individuals as contained in the Borrower’s records and any other Entity Next Payday considers necessary on the Borrower’s indebtedness to Next Payday
                            <br>16.5 The Borrower authorizes Next Payday to access any information available to process his or her application, and permission to register details of the trend of the Borrower’s account with any credit bureau, and the Borrower waives any claims he or she may have against Next Payday in respect of such disclosure
                            <br>16.6 Next Payday reserves the right to unilaterally review the facility including pricing, prepayment and past due obligation charge from time to time in the light of changing market conditions and also to terminate this facility based on any adverse information threatening the basis of this relationship or putting the facility at the risk of loss and where the borrower is in breach of any of the terms and conditions of this facility. The Borrower shall be notified of any decision taken in this respect
                            <br>16.7 The Borrower hereby agrees and consents that such notification by Next Payday shall be by way of text messages sent to the Borrowers mobile phone numbers listed on the Borrowers account package with Next Payday or by e-mail messages sent to Borrower’s e-mail address listed on the Borrower’s account details/application with Next Payday or through any other means Next Payday may consider appropriate
                            <br>16.8 The Borrower specifically and unequivocally waives any right to contest, challenge, protest or claim upon any subsequent amendments made by Next Payday to the terms of this facility or any notification sent by way of e-mail or text message to the Borrower’s e-mail address or mobile phone numbers
                            <br>16.9 The terms and conditions of this facility are subject to applicable laws of the Federal Republic of Nigeria as prescribed from time to time and the jurisdiction of the Nigerian Courts
                            <br>16.10 Next Payday does not make any representation or warranty as to the accuracy or completeness of any due diligence reports or other reports, documents, or credit analyses prepared, or caused to be prepared, by it in connection with its activities under this facility or otherwise
                            <p>
                                <br>The Borrower confirms that he/she has read, understood and agreed to the above terms and conditions. By using this service, the Borrower indicates that he/she unconditionally accepts the terms of this agreement and agrees to abide by these terms. The Borrower also agrees that this agreement is in effect until he/she discontinues the use of the service and all financial obligations with regard to his/her use of the service has been fully fulfilled.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal: Privacy Policy -->
    <div class="modal privacy-modal fade" id="applyModalPrivacy" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>PRIVACY POLICY</h4>
                        <p>At Next Payday we treat your personal information as private and confidential. We are dedicated to protecting your privacy and providing you with the highest level of security at any point of interaction with us. This Privacy Policy describes what personal information we collect, what we do with it and how we protect it. This policy (together with our Terms and Conditions) sets out the basis on which any personal data we collect from you, or that you provide to us, will be processed by us.
                            <br>Please read the following carefully to understand our views and practices regarding your personal data and how we will treat it. By continuing to visit our website <a href="home.html" target="_blank"><b>https://www.nextpayday.co</b></a>, you accept and consent to the practices described in this policy.
                        </p>
                        <br>
                        <p><strong>1. Information We Collect</strong>
                        <br>We collect information about you from a variety of sources, such as: website visits, applications, identification documents, curriculum vitae, personal financial statements, interactions with relationship managers, and other third parties (credit bureaus, payment gateways, other financial institutions…) and other written or electronic communication reflecting information such as your name, address, passport details, identification numbers, biometric information, telephone number, occupation, assets, income and any other information we deem necessary.
                        <br>We may use your transactional account history including your account balance, payment records, and debit/credit card usage. We may also use information received from third parties such as family, solicitors, friends or employers, website/ social media pages made public by you, government agencies, regulators, supervisory or credit agencies.
                        <br>We may also collect other information such as video footages of you whenever you step into any of our branches or telephone conversations when you call any of our contact center lines.
                        <p>
                        <br><strong>2. Non- Personal Information Collected By Us</strong>
                        <br>In order to achieve our goal of providing you with the best banking service, we sometimes collect certain information during your visits to perform certain tasks such as grant you access to some parts of our website or conduct research on your behavior on our site in order to improve our services. We will not disclose your information to any person outside our organization except as described in this Privacy Policy.
                        <br>
                        <br><strong>3. Cookies</strong>
                        <br>Cookies are small text files stored on your computer or mobile devices whenever you visit a website. Cookies have many uses, such as memorizing your preferences to tailor your experiences while on our site– or to help us analyse our website traffic.
                        <br>Next Payday uses cookies to improve your experience while on this site. We would like to let you know a few things about our cookies:
                        <p>
                        <br><i>SOME COOKIES ARE ESSENTIAL TO ACCESS CERTAIN AREAS OF THIS SITE</i>
                        <br>We use analytics cookies to help us understand how you use our site to discover what content is most useful to you.
                        <br>
                        <br><strong>4. Use of Your Personal Information</strong>
                        <br>The collection and use of personal data by Next Payday is guided by certain principles. These principles state that personal data should:
                        <br>●	be processed fairly, lawfully and in a transparent manner
                        <br>●	be obtained for a specified and lawful purpose and shall not be processed in any manner incompatible with such purposes
                        <br>●	be adequate, relevant and limited to what is necessary to fulfil the purpose of processing
                        <br>●	be accurate and where necessary, up-to-date. In the event data is inaccurate, steps should be taken to rectify or erase such data
                        <br>●	not be kept for longer than necessary for the purpose of processing
                        <br>●	be processed in accordance with the data subject’s rights
                        <br>●	be kept safe from unauthorized processing, and accidental loss, damage or destruction using adequate technical and organizational measures
                        <p>
                        <br>Any personal information provided by you to Next Payday will be used with your consent, or under the following under listed instances:
                        <br>●	Cases where processing of personal data is required for the fulfilment of a contractual obligation
                        <br>●	Cases where processing of personal data is required for compliance with legal and/or regulatory requirements
                        <br>●	Cases where processing is required to protect your vital interest or that of any other natural person
                        <br>●	Cases where processing is required for an activity to be carried in significant public interest
                        <br>●	Cases where processing is required for legitimate interests of Next Payday or a third party insofar as this does not conflict with the requirements for the protection of your personal data
                        <p>
                        <br>YOUR PERSONAL INFORMATION IS USED IN:
                        <br>●	Updating and enhancing Next Payday’s records
                        <br>●	Executing your instructions
                        <br>●	Establishing your identity and assessing applications for our products and services
                        <br>●	Pricing and designing our products and services
                        <br>●	Administering our products and services
                        <br>●	Managing our relationship with you
                        <p>
                        <br><strong>5. Managing Our Risks</strong>
                        <br>●	Identifying and investigating illegal activity (i.e.), such as fraud
                        <br>●	Contacting you, for example in instances where we suspect fraud on your account or when the need arises to tell you about recent occurrences in the banking sector or some event(s) of significance
                        <br>●	Conducting and improving our businesses and improving your experience with us
                        <br>●	Reviewing credit or loan eligibility
                        <br>●	Preventing money laundering or terrorism financing activities
                        <br>●	Complying with our legal obligations and assisting government and law enforcement agencies or regulators/supervisors
                        <br>●	Identifying and informing you about other products or services that we think may be of interest to you
                        <br>●	Processing your job application if you apply for a job with us
                        <p>We may also collect, use and exchange your information in other ways permitted by law.
                        <br>
                        <br><strong>6. Automated Processing</strong>
                        <br>We sometimes use automated systems and software to help us reach decisions about you, for example, to make credit decisions, to carry out security, fraud and money laundering checks, or to process your data when you apply for some of our products and services.
                        <br>This type of processing is carried out under lawful basis and you can contact us to request that automated processing be reviewed by a human being if you detect any inaccuracies in your personal data.
                        <p>
                        <br><strong>7. Information We Share</strong>
                        <br>We may share the information about you and your dealings with us, to the extent permitted by law, with the following:
                        <br>●   Next Payday Branches and Subsidiaries
                        <br>●	Regulators/Supervisors, Government Agencies/courts - It may be necessary by law, legal process, litigation, and/or requests from public and governmental authorities within or outside your country of residence for Next Payday to disclose your personal information. We may also disclose information about you if we determine that for purposes of national security, law enforcement, or other issues of public importance, disclosure is necessary or appropriate
                        <br>●	External Auditors
                        <br>●	Next Payday staff
                        <br>●	Credit Agencies
                        <br>●	Correspondent banks
                        <br>●	Next Payday’s strategic partners’/service providers – for the purpose of improving and providing our products and services to you. Your Personal information will not be shared with third parties for their marketing purposes
                        <p>
                        <br>We may also disclose information about you if we determine that disclosure is reasonably necessary to enforce our terms and conditions or protect our operations or users. Additionally, in the event of a reorganization, merger, or sale we may transfer any and all personal information we collect to the relevant third party.
                        <br>Be aware that information about our customers and their usage of our website is not shared with third parties for their marketing purposes. We do not disclose any information about any user’s usage of our web site except in specific cases, and we do not share information with any unaffiliated third parties for marketing purposes unless you expressly give us permission to do so.
                        <p>
                        <br><strong>8. How We Protect Your Information</strong>
                        <br>We take appropriate technical and organizational measures to prevent loss, unauthorized access, misuse, modification or disclosure of information under our control. This may include the use of encryption, access controls and other forms of security to ensure that your data is protected. We require all parties including our staff and third-parties processing data on our behalf to comply with relevant policies and guidelines to ensure confidentiality and that information is protected in use, when stored and during transmission. Our security controls and processes are also regularly updated to meet and exceed industry standards.
                        <p>
                        <br><strong>9. Where We Store Your Information</strong>
                        <br>All Personal Information you provide to us is stored on our secure servers as well as secure physical locations and cloud infrastructure (where applicable). The data that we collect from you may be transferred to and stored at a destination outside Nigeria or the European Economic Area (“EEA”). Whenever your information is transferred to another location, we will take all necessary steps to ensure that your data is handled securely and in accordance with this privacy policy.
                        <p>
                        <br><strong>10. How Long We Store Your Information</strong>
                        <br>We retain your data for as long as is necessary for the purpose(s) that it was collected. Storage of your data is also determined by legal, regulatory, administrative or operational requirements. We only retain information that allows us comply with legal and regulatory requests for certain data, meet business and audit requirements, respond to complaints and queries, or address disputes or claims that may arise.
                        <br>Data which is not retained is securely destroyed when it is identified that is no longer needed for the purposes for which it was collected.
                        <p>
                        <br><strong>11. Your Rights</strong>
                        <br>You have certain rights available to you, these include:
                        <br>●	The right to access your personal information held by us. Your right of access can be exercised by sending an email to <a href="mailto:dpo@nextpayday.co"><b>dpo@nextpayday.co</b></a>
                        <br>●	The right to rectify inaccurate or incomplete information
                        <br>●	Withdraw consent for processing in cases where consent has previously been given
                        <br>●	Restrict or object to processing of your personal data. We might continue to process your data if there are valid legal or operational reasons
                        <br>
                        <br>YOU ALSO HAVE THE RIGHT TO:
                        <br>●	Request that your personal data be made available to you in a common electronic format and/or request that such data be sent to a third party
                        <br>●	Request that your information be erased. We might continue to retain such data if there are valid legal, regulatory or operational reasons
                        <p>
                        <br><strong>12. Maintaining Accurate Information</strong>
                        <br>Keeping your account information accurate and up to date is very important. You have access to your account information, which includes your contact information, account balances, transactions and similar information through various means, such as emails, SMS, Remote Account access etc.
                        <br>If you discover any inaccuracies in your personal information, please promptly notify us, via our e-channels, branch network or Contact Centre, and provide the required documentary evidence, to enable us to implement the necessary updates or changes.
                        <p>
                        <br><strong>13. Applicability of This Privacy Policy</strong>
                        <br>Next Payday and its subsidiary.
                        <p>
                        <br><b>●	Third-Party Sites and Services:</b> Next Payday’s websites, products, applications, and services may contain links to third-party websites, products and services. Our products and services may also use or offer products or services from third parties. Information collected by third parties, which may include such things as location data or contact details is governed by their privacy practices and Next Payday will not be liable for any breach of confidentiality or privacy of your information on such sites. We encourage you to learn about the privacy practices of those third parties
                        <br>
                        <br><b>●	Social Media Platforms:</b> Next Payday may interact with registered users of various social media platforms, including Facebook, Twitter, Instagram, and YouTube. Please note that any content you post to such social media platforms (e.g. pictures, information or opinions), as well as any personal information that you otherwise make available to users (e.g. your profile) is subject to the applicable social media platform’s terms of use and privacy policies. We recommend that you review these information carefully in order to better understand your rights and obligations with regard to such content
                        <p>
                        <br><strong>14. Maintaining Accurate Information</strong>
                        <br>Keeping your account information accurate and up to date is very important. You have access to your loan information, which includes your contact information, loan balances, transaction statement and similar information.
                        <br>If you discover any inaccuracies in your personal information, please promptly notify us, via our branch network or Contact Centre, and provide the required documentary evidence, to enable us to implement the necessary updates or changes.
                        <p>
                        <br><strong>15. Privacy of Children</strong>
                        <br>Next Payday Policy contains the following disclosure statement for children:
                        <br>●	"Next Payday respects the privacy of children. We do not knowingly collect names, email addresses or any other personally identifiable information from children. We do not knowingly market to children nor do we allow children under 18 to open online accounts"
                        <p>
                        <br><strong>16. Promotional Messages</strong>
                        <br>Next Payday may sometimes contact you with products or services that we think may be of interest to you. If you don’t want to receive such promotional materials from us, you can opt out at any time by sending an email to <a href="mailto:dpo@nextpayday.co"><b>dpo@nextpayday.co</b></a>
                        <p>
                        <br><strong>17. Privacy Policy Changes</strong>
                        <br>This policy may be revised on an ad-hoc basis to reflect the legal, regulatory and operating environment and such revised versions will automatically become applicable to you. We will post any revisions we make to our Privacy Policy on this page and such revised policy becomes effective as at the time it is posted. We also encourage you to check this page from time to time for updates to this policy.
                        <p>
                        <br><strong>CONTACT:</strong>
                        <br>Questions, comments and requests regarding this privacy policy are welcomed and should be addressed to <a href="mailto:dpo@nextpayday.co"><b>dpo@nextpayday.co</b></a>
                        <br>To contact our Data Protection Officer, kindly address your request to <b>“The Data Protection Officer”</b> at 11A Igbodo Street, Old GRA Port Harcourt.
                    </div>
            </div>
        </div>
    </div>
<!-- copyright area -->
<div class="copyright pt-25 pb-25">
    <div class="container">
        <div class="section-title">
                <div class="copyright-text">
                    <p>Copyright&copy; Next Payday 2023.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</footer>

<!-- Back to top button -->
<a id="back-to-top" title="Back to Top"></a>
<!-- JS here -->
<script type="text/javascript" src="{{ asset('new_auth_assets/js/jquery-3.6.0.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/preloader.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/bootstrap.bundle.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/jquery.smoothscroll.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/jquery.waypoints.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/jquery.counterup.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/jquery.nice-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/slick.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/parallax.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/jquery.parallax-scroll.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/wow.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_auth_assets/js/custom.js') }}"></script>
</body>
</html>