<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nextpayday | Investors </title>
    <link
      rel="stylesheet"
      href="{{asset('investorpage/bootstrap/bootstrap-5.1.3-dist/css/bootstrap.min.css')}}"
    />
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link rel="stylesheet" href="{{asset('investorpage/css/main.css')}}" />
  </head>
  <body>
    <header style="height: auto" class="header">
      <nav class="navbar navbar-expand-lg px-5 navbar-light bg-light">
        <a class="navbar-brand" href="{{route('investors.landingpage')}}">
          <svg
            width="110"
            height="53"
            viewBox="0 0 110 53"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <g clip-path="url(#clip0)">
              <path
                d="M34.6421 4.93442V39.9124H24.9016L11.5488 24.0307V39.9124H0.00415039V4.93442H9.74045L23.0975 20.8161V4.93442H34.6421Z"
                fill="#0d6efd"
              />
              <path
                d="M71.1426 18.1219C71.1426 26.2126 64.9183 31.3053 55.1276 31.3053H50.0418V39.8957H38.1958V4.91772H55.1109C64.9183 4.93438 71.1426 10.0312 71.1426 18.1219ZM59.1963 18.1219C59.1963 15.5735 57.6391 14.0245 54.3784 14.0245H50.0418V22.2152H54.3616C57.6391 22.2152 59.1963 20.6661 59.1963 18.1219Z"
                fill="#0d6efd"
              />
              <path
                d="M79.4097 23.8517L79.573 24.0432L91.712 14.849L91.5153 14.6117L79.4097 23.8517ZM72.8547 39.8957H90.1757C101.921 39.8957 110.004 33.2333 110.004 22.4067C110.012 20.7204 109.797 19.0403 109.364 17.4099C108.964 18.0691 108.529 18.7071 108.062 19.3212C107.767 19.7083 107.451 20.0794 107.116 20.433C106.139 21.527 105.045 22.5127 103.855 23.3728C100.666 25.7963 96.4755 27.4328 92.3525 28.4072C91.7037 28.5612 91.8209 28.5446 91.825 29.24V33.3707C91.5458 33.1927 91.2787 32.9966 91.0255 32.7836L90.2679 32.2089C86.9192 29.7105 83.424 26.9789 80.046 24.4846L79.7698 24.2639C79.7412 24.2363 79.716 24.2056 79.6945 24.1723L81.9757 22.4817C83.1854 21.5989 84.5249 20.5079 85.743 19.6168C86.2704 19.2379 86.735 18.8506 87.254 18.4717L91.8167 15.0489C91.8167 15.661 91.7287 19.521 91.871 19.775C92.1766 20.0457 94.9728 19.9791 95.542 19.9541C99.7279 19.7876 103.859 18.5342 106.986 15.9358C107.465 15.5456 107.92 15.1285 108.351 14.6866L108.267 14.5117C107.336 15.4705 106.295 16.3173 105.165 17.0351C102.009 19.0547 97.7396 19.9208 93.8886 19.8C93.3905 19.8 92.4278 19.671 92.0134 19.7168L91.9924 19.4627C97.3085 19.6543 101.62 18.988 105.069 16.7145C106.176 15.986 107.188 15.1245 108.083 14.1495C105.027 8.31979 98.5224 4.93442 90.1633 4.93442H72.8547V39.8957Z"
                fill="#0d6efd"
              />
              <path
                d="M5.29097 46.6998V53H4.7426L0.669777 47.8865V53H0V46.6998H0.552561L4.63377 51.8091V46.6998H5.29097Z"
                fill="#0d6efd"
              />
              <path
                d="M17.095 52.4212V53H12.6204V46.6998H16.9736V47.2744H13.3026V49.5147H16.555V50.0851H13.2817V52.4212H17.095Z"
                fill="#0d6efd"
              />
              <path
                d="M27.8232 53L25.7806 50.2267L23.7462 53H22.9761L25.3829 49.7603L23.1309 46.6998H23.8969L25.8057 49.2732L27.706 46.6998H28.4302L26.1824 49.727L28.5976 52.9833L27.8232 53Z"
                fill="#0d6efd"
              />
              <path
                d="M36.0108 47.2744H33.7839V46.6998H38.9032V47.2744H36.6806V53H36.0108V47.2744Z"
                fill="#0d6efd"
              />
              <path
                d="M50.4604 48.9858C50.4604 50.3891 49.4014 51.2635 47.7103 51.2635H46.4545V53H44.9895V46.6998H47.7271C49.3847 46.6998 50.4604 47.5742 50.4604 48.9858ZM48.9786 48.9858C48.9786 48.2946 48.5266 47.8865 47.6308 47.8865H46.4378V50.0726H47.6308C48.5098 50.0726 48.9619 49.6687 48.9619 48.9858H48.9786Z"
                fill="#0d6efd"
              />
              <path
                d="M61.0256 51.6508H58.0955L57.5346 53H56.0361L58.8574 46.6998H60.3057L63.1353 53H61.5991L61.0256 51.6508ZM60.561 50.5432L59.5606 48.1322L58.556 50.5432H60.561Z"
                fill="#0d6efd"
              />
              <path
                d="M72.1182 50.7681V53H70.6531V50.7514L68.2043 46.6998H69.7574L71.4317 49.498L73.1061 46.6998H74.546L72.1182 50.7681Z"
                fill="#0d6efd"
              />
              <path
                d="M81.1594 46.6998H83.7379C85.7638 46.6998 87.1284 47.9948 87.1284 49.8478C87.1284 51.7008 85.7638 53 83.7379 53H81.1594V46.6998ZM83.7002 52.4212C85.3745 52.4212 86.4712 51.3594 86.4712 49.8478C86.4712 48.3363 85.3829 47.2744 83.7002 47.2744H81.8291V52.4212H83.7002Z"
                fill="#0d6efd"
              />
              <path
                d="M97.7607 51.3177H94.2321L93.4744 52.9833H92.7754L95.672 46.6831H96.3292L99.2258 52.9833H98.5184L97.7607 51.3177ZM97.5137 50.7764L95.9985 47.3952L94.479 50.7764H97.5137Z"
                fill="#0d6efd"
              />
              <path
                d="M107.355 50.8222V53H106.693V50.8222L104.161 46.6998H104.877L107.049 50.235L109.217 46.6998H109.887L107.355 50.8222Z"
                fill="#0d6efd"
              />
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M72.8547 4.93851C86.1909 -0.23324 101.649 -3.77265 102.068 7.15798C98.5182 2.36516 80.0377 5.20501 72.8547 4.93851Z"
                fill="#243E90"
              />
            </g>
            <defs>
              <clipPath id="clip0">
                <rect width="110" height="53" fill="#0d6efd" />
              </clipPath>
            </defs>
          </svg>
        </a>

        <button
          class="navbar-toggler"
          type="button"
          data-toggle="collapse"
          data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="{{route('investors.landingpage')}}">Home </a>
            </li>
            <!-- <li class="nav-item">
              <a class="nav-link" href="#">About Us</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">FAQ </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Blog</a>
            </li> -->
          </ul>

          <a href="{{route('investors.signin')}}" id="login" class="btn my-2 my-sm-0"> Login </a>
        </div>
      </nav>
    </header>
@yield('content')    
<section style="margin-top: auto;" class="footer">
      <div class="footer_wrapper">
        <div class="row">
          <div class="col-lg-6 col-md-6 col-12">
            <span class="footer_wrapper-left">
              <div class="footer_box">
                <svg
                  width="110"
                  height="53"
                  viewBox="0 0 110 53"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <g clip-path="url(#clip0)">
                    <path
                      d="M34.6421 4.93442V39.9124H24.9016L11.5488 24.0307V39.9124H0.00415039V4.93442H9.74045L23.0975 20.8161V4.93442H34.6421Z"
                      fill="#fFF"
                    />
                    <path
                      d="M71.1426 18.1219C71.1426 26.2126 64.9183 31.3053 55.1276 31.3053H50.0418V39.8957H38.1958V4.91772H55.1109C64.9183 4.93438 71.1426 10.0312 71.1426 18.1219ZM59.1963 18.1219C59.1963 15.5735 57.6391 14.0245 54.3784 14.0245H50.0418V22.2152H54.3616C57.6391 22.2152 59.1963 20.6661 59.1963 18.1219Z"
                      fill="#0d6efd"
                    />
                    <path
                      d="M79.4097 23.8517L79.573 24.0432L91.712 14.849L91.5153 14.6117L79.4097 23.8517ZM72.8547 39.8957H90.1757C101.921 39.8957 110.004 33.2333 110.004 22.4067C110.012 20.7204 109.797 19.0403 109.364 17.4099C108.964 18.0691 108.529 18.7071 108.062 19.3212C107.767 19.7083 107.451 20.0794 107.116 20.433C106.139 21.527 105.045 22.5127 103.855 23.3728C100.666 25.7963 96.4755 27.4328 92.3525 28.4072C91.7037 28.5612 91.8209 28.5446 91.825 29.24V33.3707C91.5458 33.1927 91.2787 32.9966 91.0255 32.7836L90.2679 32.2089C86.9192 29.7105 83.424 26.9789 80.046 24.4846L79.7698 24.2639C79.7412 24.2363 79.716 24.2056 79.6945 24.1723L81.9757 22.4817C83.1854 21.5989 84.5249 20.5079 85.743 19.6168C86.2704 19.2379 86.735 18.8506 87.254 18.4717L91.8167 15.0489C91.8167 15.661 91.7287 19.521 91.871 19.775C92.1766 20.0457 94.9728 19.9791 95.542 19.9541C99.7279 19.7876 103.859 18.5342 106.986 15.9358C107.465 15.5456 107.92 15.1285 108.351 14.6866L108.267 14.5117C107.336 15.4705 106.295 16.3173 105.165 17.0351C102.009 19.0547 97.7396 19.9208 93.8886 19.8C93.3905 19.8 92.4278 19.671 92.0134 19.7168L91.9924 19.4627C97.3085 19.6543 101.62 18.988 105.069 16.7145C106.176 15.986 107.188 15.1245 108.083 14.1495C105.027 8.31979 98.5224 4.93442 90.1633 4.93442H72.8547V39.8957Z"
                      fill="#0d6efd"
                    />
                    <path
                      d="M5.29097 46.6998V53H4.7426L0.669777 47.8865V53H0V46.6998H0.552561L4.63377 51.8091V46.6998H5.29097Z"
                      fill="#0d6efd"
                    />
                    <path
                      d="M17.095 52.4212V53H12.6204V46.6998H16.9736V47.2744H13.3026V49.5147H16.555V50.0851H13.2817V52.4212H17.095Z"
                      fill="#0d6efd"
                    />
                    <path
                      d="M27.8232 53L25.7806 50.2267L23.7462 53H22.9761L25.3829 49.7603L23.1309 46.6998H23.8969L25.8057 49.2732L27.706 46.6998H28.4302L26.1824 49.727L28.5976 52.9833L27.8232 53Z"
                      fill="#0d6efd"
                    />
                    <path
                      d="M36.0108 47.2744H33.7839V46.6998H38.9032V47.2744H36.6806V53H36.0108V47.2744Z"
                      fill="0d6efd"
                    />
                    <path
                      d="M50.4604 48.9858C50.4604 50.3891 49.4014 51.2635 47.7103 51.2635H46.4545V53H44.9895V46.6998H47.7271C49.3847 46.6998 50.4604 47.5742 50.4604 48.9858ZM48.9786 48.9858C48.9786 48.2946 48.5266 47.8865 47.6308 47.8865H46.4378V50.0726H47.6308C48.5098 50.0726 48.9619 49.6687 48.9619 48.9858H48.9786Z"
                      fill="white"
                    />
                    <path
                      d="M61.0256 51.6508H58.0955L57.5346 53H56.0361L58.8574 46.6998H60.3057L63.1353 53H61.5991L61.0256 51.6508ZM60.561 50.5432L59.5606 48.1322L58.556 50.5432H60.561Z"
                      fill="white"
                    />
                    <path
                      d="M72.1182 50.7681V53H70.6531V50.7514L68.2043 46.6998H69.7574L71.4317 49.498L73.1061 46.6998H74.546L72.1182 50.7681Z"
                      fill="white"
                    />
                    <path
                      d="M81.1594 46.6998H83.7379C85.7638 46.6998 87.1284 47.9948 87.1284 49.8478C87.1284 51.7008 85.7638 53 83.7379 53H81.1594V46.6998ZM83.7002 52.4212C85.3745 52.4212 86.4712 51.3594 86.4712 49.8478C86.4712 48.3363 85.3829 47.2744 83.7002 47.2744H81.8291V52.4212H83.7002Z"
                      fill="#0d6efd"
                    />
                    <path
                      d="M97.7607 51.3177H94.2321L93.4744 52.9833H92.7754L95.672 46.6831H96.3292L99.2258 52.9833H98.5184L97.7607 51.3177ZM97.5137 50.7764L95.9985 47.3952L94.479 50.7764H97.5137Z"
                      fill="#0d6efd"
                    />
                    <path
                      d="M107.355 50.8222V53H106.693V50.8222L104.161 46.6998H104.877L107.049 50.235L109.217 46.6998H109.887L107.355 50.8222Z"
                      fill="#0d6efd"
                    />
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M72.8547 4.93851C86.1909 -0.23324 101.649 -3.77265 102.068 7.15798C98.5182 2.36516 80.0377 5.20501 72.8547 4.93851Z"
                      fill="#243E90"
                    />
                  </g>
                  <defs>
                    <clipPath id="clip0">
                      <rect width="110" height="53" fill="white" />
                    </clipPath>
                  </defs>
                </svg>

                <p class="contact_address mt-3">
                  No. 6 Abuja Lane, Legacy Centre, D/Line Port Harcourt,
                  Rivers State.
                  <br />
                  <br />
                  +234 (0) 809 500 0667
                </p>
                <div class="footer_block mt-s">
                  <a
                    href="https://facebook.com/nextpaydayng/"
                    target="_blank"
                    class="footer_icon"
                    rel="noreferrer"
                    ><svg
                      xmlns="http://www.w3.org/2000/svg"
                      focusable="false"
                      style="transform: rotate(360deg)"
                      width="0.54em"
                      height="1em"
                      preserveAspectRatio="xMidYMid meet"
                      viewBox="0 0 896 1664"
                    >
                      <path
                        d="M895 12v264H738q-86 0-116 36t-30 108v189h293l-39 296H592v759H286V905H31V609h255V391q0-186 104-288.5T667 0q147 0 228 12z"
                        fill="currentColor"
                      ></path></svg
                  ></a>
                  <a
                    target="_blank"
                    class="footer_icon"
                    href="https://instagram.com/nextpaydayng/"
                    rel="noreferrer"
                    ><svg
                      xmlns="http://www.w3.org/2000/svg"
                      focusable="false"
                      style="transform: rotate(360deg)"
                      width="1em"
                      height="1em"
                      preserveAspectRatio="xMidYMid meet"
                      viewBox="0 0 1536 1536"
                    >
                      <path
                        d="M1024 768q0-106-75-181t-181-75t-181 75t-75 181t75 181t181 75t181-75t75-181zm138 0q0 164-115 279t-279 115t-279-115t-115-279t115-279t279-115t279 115t115 279zm108-410q0 38-27 65t-65 27t-65-27t-27-65t27-65t65-27t65 27t27 65zM768 138q-7 0-76.5-.5t-105.5 0t-96.5 3t-103 10T315 169q-50 20-88 58t-58 88q-11 29-18.5 71.5t-10 103t-3 96.5t0 105.5t.5 76.5t-.5 76.5t0 105.5t3 96.5t10 103T169 1221q20 50 58 88t88 58q29 11 71.5 18.5t103 10t96.5 3t105.5 0t76.5-.5t76.5.5t105.5 0t96.5-3t103-10t71.5-18.5q50-20 88-58t58-88q11-29 18.5-71.5t10-103t3-96.5t0-105.5t-.5-76.5t.5-76.5t0-105.5t-3-96.5t-10-103T1367 315q-20-50-58-88t-88-58q-29-11-71.5-18.5t-103-10t-96.5-3t-105.5 0t-76.5.5zm768 630q0 229-5 317q-10 208-124 322t-322 124q-88 5-317 5t-317-5q-208-10-322-124T5 1085q-5-88-5-317t5-317q10-208 124-322T451 5q88-5 317-5t317 5q208 10 322 124t124 322q5 88 5 317z"
                        fill="currentColor"
                      ></path></svg
                  ></a>
                  <a
                    rel="noreferrer"
                    target="_blank"
                    class="footer_icon"
                    href="https://www.linkedin.com/company/taskpitch-com/"
                    ><svg
                      xmlns="http://www.w3.org/2000/svg"
                      focusable="false"
                      style="transform: rotate(360deg)"
                      width="1.03em"
                      height="1em"
                      preserveAspectRatio="xMidYMid meet"
                      viewBox="0 0 1536 1504"
                    >
                      <path
                        d="M349 497v991H19V497h330zm21-306q1 73-50.5 122T184 362h-2q-82 0-132-49T0 191q0-74 51.5-122.5T186 20t133 48.5T370 191zm1166 729v568h-329V958q0-105-40.5-164.5T1040 734q-63 0-105.5 34.5T871 854q-11 30-11 81v553H531q2-399 2-647t-1-296l-1-48h329v144h-2q20-32 41-56t56.5-52t87-43.5T1157 474q171 0 275 113.5T1536 920z"
                        fill="currentColor"
                      ></path></svg
                  ></a>
                  <a
                    target="_blank"
                    class="footer_icon"
                    href="https://twitter.com/nextpaydayNG/"
                    rel="noreferrer"
                    ><svg
                      xmlns="http://www.w3.org/2000/svg"
                      focusable="false"
                      style="transform: rotate(360deg)"
                      width="1.25em"
                      height="1em"
                      preserveAspectRatio="xMidYMid meet"
                      viewBox="0 0 1600 1280"
                    >
                      <path
                        d="M1588 152q-67 98-162 167q1 14 1 42q0 130-38 259.5T1273.5 869T1089 1079.5t-258 146t-323 54.5q-271 0-496-145q35 4 78 4q225 0 401-138q-105-2-188-64.5T189 777q33 5 61 5q43 0 85-11q-112-23-185.5-111.5T76 454v-4q68 38 146 41q-66-44-105-115T78 222q0-88 44-163q121 149 294.5 238.5T788 397q-8-38-8-74q0-134 94.5-228.5T1103 0q140 0 236 102q109-21 205-78q-37 115-142 178q93-10 186-50z"
                        fill="currentColor"
                      ></path></svg
                  ></a>
                </div>
              </div>

              <div class="footer_box">
                <h4 class="footer_title">Quick Links</h4>
                <ul class="footer_list">
                  <li>
                    <a href="https://nextpayday.ng" class="footer_link">
                      <i></i> Borrowers</a
                    >
                  </li>
                  <li>
                    <a href="https://app.nextpayday.ng/investor/signup" class="footer_link">
                      <i></i> Investors</a
                    >
                  </li>
                  
                  <li>
                    <a href="https://app.nextpayday.ng/affiliates/login" class="footer_link">
                      <i></i> Affilliates</a
                    >
                  </li>
                </ul>
              </div>
            </span>
          </div>
          <div class="col-lg-6 col-md-6 col-12">
            <span class="footer_wrapper-right">
              <div class="footer_box">
                <h4 class="footer_title">Company</h4>
                <ul class="footer_list">
                  <li>
                    <a href="{{route('investors.landingpage')}}" class="footer_link"> <i></i> About Us</a>
                  </li>
                  <li>
                    <a href="https://nextpayday.ng/faqs" class="footer_link"> <i></i> F.A.Q</a>
                  </li>
                  <li>
                    <a href="https://medium.com/@nextpayday" class="footer_link"> <i></i> Blog</a>
                  </li>
                </ul>
              </div>
              <div class="footer_box">
                <h4 class="footer_title">Legal</h4>
                <ul class="footer_list">
                  <li>
                    <a href="https://nextpayday.ng/terms/lenders/" class="footer_link">
                      <i></i> Terms of Services</a>
                  </li>
                  
                </ul>
              </div>
            </span>
          </div>
        </div>
      </div>
    </section>
    <script>
          @if(Session::has('success'))
          toastr.options =
          {
            "closeButton" : true,
            "progressBar" : true
          }
              toastr.success("{{ session('success') }}");
          @endif

          @if(Session::has('error'))
          toastr.options =
          {
            "closeButton" : true,
            "progressBar" : true
          }
              toastr.error("{{ session('error') }}");
          @endif

          @if(Session::has('info'))
          toastr.options =
          {
            "closeButton" : true,
            "progressBar" : true
          }
              toastr.info("{{ session('info') }}");
          @endif

          @if(Session::has('warning'))
          toastr.options =
          {
            "closeButton" : true,
            "progressBar" : true
          }
              toastr.warning("{{ session('warning') }}");
          @endif
    </script>
    <script src="{{asset('investorpage/js/jquery.min.js')}}"></script>
    <script src="{{asset('investorpage/js/main.js')}}"></script>
    <script src="{{asset('investorpage/js/slick.min.js')}}"></script>
    <script src="{{asset('investorpage//bootstrap/bootstrap-5.1.3-dist/js/bootstrap.min.js')}}"></script>
    <script>
      $(".navbar-toggler").click(function () {
        $("#navbarSupportedContent").slideToggle();
      });
      $("#navbar-toggler2").click(function () {
        $("#navbarSupportedContent2").slideToggle();
      });

      var startY = 500;

      $(window).scroll(function () {
        checkY();
      });

      function checkY() {
        if ($(window).scrollTop() > startY) {
          $(".top_navbar-cover").slideDown(300);
        } else {
          $(".top_navbar-cover").slideUp();
        }
      }

      // Do this on load just in case the user starts half way down the page
      checkY();

      $(".select_btn").click(function () {
        $(".select_note").slideDown();
      });
      $(".select_close").click(function () {
        $(".select_note").slideUp();
      });
    </script>
    <script>
      $(".navbar-toggler").click(function () {
        $("#navbarSupportedContent").slideToggle();
      });
    </script>
    
  </body>
</html>