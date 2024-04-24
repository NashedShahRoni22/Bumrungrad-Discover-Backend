<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bumrungrad Email</title>
  </head>
  <style>
    .header-text {
      font-size: 20px;
      font-weight: bold;
      color: #29286e;
    }
  </style>
  <body style="font-family: 'Poppins', sans-serif">
    <table align="center" cellpadding="" cellspacing="" class="" width="850">
      <tr style="
            background-image: url('https://i.postimg.cc/pTh6ybHL/email-back.png');
            background-repeat: no-repeat;
            background-size: cover;
          ">
        <td style="background-color:#d2eaef; opacity: 0.90;"
          
        >
          <!-- First Table Start -->

          <table cellpadding="25" cellspacing="" width="100%" ">
            <tr>
              <td>
                <table cellpadding="" cellspacing="" class="" width="100%">
                  <tr>
                    <td>
                      <a href=""
                        ><img
                          width="280"
                          height="70"
                          src="https://i.postimg.cc/QNq0cKN9/Bumrungrad-Logo-000.png"
                          alt="Bumrungrad logo"
                          srcset=""
                      /></a>
                    </td>
                  </tr>
                </table>
              </td>
              <td>
                <table
                  cellpadding=""
                  cellspacing=""
                  style="margin-left: 30px"
                  width="100%"
                >
                  <tr>
                    <td align="center">
                      <p class="header-text">
                        Bumrungrad International Hospital <br /><span
                          style="font-size: 18px; font-weight: 500"
                          >33 Sukhumvit 3, Wattana, Bangkok 10110 Thailand</span
                        >
                      </p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- First Table End -->

          <!-- Second Table Start -->

          <table cellpadding="35" cellspacing="" width="100%">
            <tr>
              <td>
                <table cellpadding="0" cellspacing="" class="" width="280px">
                  <tr>
                    <td>
                      <p
                        style="
                          font-size: 22px;
                          font-weight: 600;
                          color: #29286e;
                        "
                      >
                        Appointment For
                      </p>
                      <li style="line-height: 21px">
                        Doctor: {{ $mail_data['doctor'] }}
                      </li>
                      <li style="line-height: 21px">Specialty: {{ $mail_data['specialty'] }}</li>
                      <li style="line-height: 21px">Phone: +8801324-418100</li>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p
                        style="
                          font-size: 22px;
                          font-weight: 600;
                          color: #29286e;
                        "
                      >
                        Appointment Schedule
                      </p>
                      <li style="line-height: 21px">
                       First Date: {{ $mail_data['selectedDate'] }}
                      </li>
                      <li style="line-height: 21px">First Shift: {{ $mail_data['shift'] }}</li>
                      <li style="line-height: 21px">
                        Second Date: {{ $mail_data['selectedDate2'] }}
                      </li>
                      <li style="line-height: 21px">Second Shift: {{ $mail_data['shift2'] }}</li>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p
                        style="
                          font-size: 22px;
                          font-weight: 600;
                          color: #29286e;
                        "
                      >
                      Patient Information
                      </p>
                      <li style="line-height: 21px">
                      Name: {{ $mail_data['PataientFirstName'] }} {{ $mail_data['PataientLastName'] }}

                      </li>
                      <li style="line-height: 21px">DOB: {{ $mail_data['PataientDob'] }}</li>
                      <li style="line-height: 21px">
                      Gender: {{ $mail_data['PataientGender'] }} </li>
                      <li style="line-height: 21px">Citizenship: {{ $mail_data['PataientCitizenship'] }}</li>
                      <li style="line-height: 21px">Country: {{ $mail_data['country'] }}</li>
                      <li style="line-height: 21px">Email: {{ $mail_data['PataientEmail'] }}</li>
                      <li style="line-height: 21px">Phone: {{ $mail_data['PataientPhone'] }}</li>
                    </td>
                  </tr>
                </table>
              </td>
              <td>
                <table cellpadding="" cellspacing="" class="" width="100%">
                  <tr>
                    <td class="header-md">
                      <p
                        style="
                          font-size: 22px;
                          font-weight: 600;
                          color: #29286e;
                        "
                      >
                        Find Us Here
                      </p>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <img
                        src="https://i.postimg.cc/MpND4j9K/002-telephone-call-removebg-preview.png"
                        alt="Bumrungrad Hospital"
                        srcset=""
                        width="40"
                        height="40"
                        style="margin-left: 40px"
                      />
                    </td>
                    <td>
                      <p style="font-weight: bold;">Phone</p>
                      <p>+8801324-418100</p>
                      <p>+8801847-284860</p>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <img
                        src="https://i.postimg.cc/nLzB2V4Y/003-email-removebg-preview.png"
                        alt="Bumrungrad Hospital"
                        srcset=""
                        width="40"
                        height="40"
                        style="margin-left: 40px"
                      />
                    </td>
                    <td>
                      <p style="font-weight: bold;">Email</p>
                      <p>support@bumrungraddiscover.com</p>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <img
                        src="https://i.postimg.cc/15sgM5Pn/001-location-pin-removebg-preview.png"
                        alt="Bumrungrad Hospital"
                        srcset=""
                        width="40"
                        height="40"
                        style="margin-left: 40px"
                      />
                    </td>
                    <td>
                      <p style="font-weight: bold;">Location</p>
                      <p>
                        Rupayan Prime Tower 10th Floor <br />
                        (Lift-9) House:02, Road: 07,<br />
                        Green Road Dhanmondi, Dhaka-1205
                      </p>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <img
                        src="https://i.postimg.cc/pLjgTxhC/004-web-removebg-preview.png"
                        alt="Bumrungrad Hospital"
                        srcset=""
                        width="40"
                        height="40"
                        style="margin-left: 40px"
                      />
                    </td>
                    <td>
                      <p style="font-weight: bold;">Website</p>
                      <p>bumrungraddiscover.com</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- Second Table End -->

          <!-- Third Table Start -->

          <table
            cellpadding=""
            cellspacing="10"
            style="padding-bottom: 20px;"
            width="100%"
          >
            <tr>
              <td align="center">
                <img
                  src="https://i.postimg.cc/MZ1HKBf2/email-img.png"
                  alt=""
                  srcset=""
                  width="680"
                  height="320"
                  style="border-radius: 10px;"
                />
              </td>
            </tr>
          </table>

          <!-- Third Table End -->

          <!-- 4th Table Start -->

          <table cellpadding="20" cellspacing="30" width="100%">
            <tr>
              <td
                style="
                  background-color: #c0c2c7;
                  padding: 0px 20px;
                  border-radius: 10px;
                "
              >
                <p style="text-align: center; font-weight: bold">Disclaimer</p>
                <p style="font-size: 14px; text-align: justify; ">
                  DIMS is a Medical Tourism Facilitator and does not provide
                  direct treatment advice. We connect you with top-quality,
                  licensed hospitals. Any treatment plans come solely from
                  licensed doctors at our partner hospitals. DIMS holds no
                  liability for advice given by third-party licensed doctors or
                  hospitals. We strongly recommend consulting your local doctor
                  to discuss treatment options provided through our platform.
                </p>
              </td>
            </tr>
          </table>

          <!-- 4th Table End -->

          <!-- 5th Table Start -->

          <table
            cellpadding="25"
            cellspacing=""
            style="background-color: #030c35;z-index: 10;"
            width="100%"
           
          >
            <tr>
              <td></td>
            </tr>
          </table>

          <!-- 5th Table Start -->
        </td>
      </tr>
    </table>
  </body>
</html>
