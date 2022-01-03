<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forget Password Mail</title>
    <style type="text/css">
    body{background-color:#eff0f0;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.12857143;color:#847f7f}p{margin-left:15px}.btn-info{color:#fff;background-color:#0685c4;border-color:#46b8da}.btn{display:inline-block;margin-bottom:0;font-weight:400;text-align:center;white-space:nowrap;vertical-align:middle;touch-action:manipulation;cursor:pointer;background-image:none;border:1px solid transparent;border-top-color:transparent;border-right-color:transparent;border-bottom-color:transparent;border-left-color:transparent;padding:6px 12px;font-size:14px;line-height:1.42857143;border-radius:4px;user-select:none}
    </style>
  </head>
<body>
<table class="table" style="width: 100%;">
  <tr>
   <td width="30%"></td>
    <td width="40%" style="background-color: white;text-align: center;padding: 25px;background-color: #eff0f0;font-size: 20px;">
          <table border="0" cellpadding="0" cellspacing="0" width="480" >
              <tr>
                  <td align="center" valign="top">
                      <a href="#">
                        <img alt="Logo" src="{{ asset('public/images/logo/family-days-app-icon.svg')}}" width="220" height="170" style="display: block;  font-family: 'Lato', Helvetica, Arial, sans-serif; color: #ffffff; font-size: 18px;" border="0">
                      </a>
                  </td>
              </tr>
          </table>
     </td>
      <td width="30%"></td>
  </tr>
 <tr>
   <td width="30%"></td>
    <td width="40%" style="border-right: 1px solid #d7d0d0;border-left: 1px solid #d7d0d0;background-color: white;">
      <p style="color: black;font-weight: 500;font-size: 20px;"><b>Hello {{ $userdetail['full_name'] }}</b></p>
      <p style="font-size: 16px;">You are receiving this email because we received a password reset request for your account.</p>
       <table class="table" style="border-collapse: collapse;margin-left: 15px;margin-right: 15px;margin-top:15px;width: 90%;">
          <tbody>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 30%;font-weight: 600;">Login Username </td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">{{ $userdetail['username'] }} / {{ $userdetail['email'] ? $userdetail['email']:'NA' }}</td>
            </tr>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 30%;font-weight: 600;">Password</td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">{{ $password }}</td>
            </tr>
          </tbody>
        </table>
      <p style="font-size: 16px;">If you did not request a password reset, no further action is required.</p>
      <p style="font-size: 16px;">Regards,</p>
      <p style="font-size: 16px;">ChoreUp</p></td>
      <td width="30%"></td>
  </tr>
    <tr>
      <td width="30%"></td>
      <td width="40%" style="border-right: 1px solid #d7d0d0;border-left: 1px solid #d7d0d0;background-color: white;text-align: center;">
        <p style="color: #9b9a9a;font-size:13px;" style="text-align: center;">Please reach out to admin@choreup.com in case of any feedback! </p>
      </td>
    <td width="30%"></td>
  </tr>
  <tr>
    <td width="100%" style="background-color: white;text-align: center;padding: 25px;background-color: #eff0f0;font-size: 14px;" colspan="3"><b style="color: #a4a2a2;">© 2020 ChoreUp. All rights reserved.</b></td>
  </tr>
</table>
</body>
</html>

