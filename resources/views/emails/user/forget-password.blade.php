<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Forget Password Mail</title>
    <style type="text/css">
      body{
         background-color: #e8e4e4;
         font-family: Arial, Helvetica, sans-serif;;
        font-size: 14px;
        line-height: 1.12857143;
        color: #847f7f;
      }
      p{
        margin-left: 15px;
      }
    </style>
  </head>
<body>
<table class="table" style="width: 100%;">
  <tr>
    <td width="20%"></td>
    <td width="60%" style="border-right: 1px solid #d7d0d0;border-left: 1px solid #d7d0d0;border-top: 1px solid #d7d0d0;background-color: white;text-align: center;"><img src="{{ asset('public/images/logo/family-days-app-icon.svg')}}" style="height: 250px;" /></td>
    <td width="20%"></td>
  </tr>
 <tr>
    <td width="20%"></td>
    <td width="60%" style="border-right: 1px solid #d7d0d0;border-left: 1px solid #d7d0d0;background-color: white;">
      <p style="color: #ca6710;font-weight: 500;font-size: 16px;">Resetting your password for family days</p>
      <p><b>Hello {{ $userdetail['full_name'] }},</b></p>
      <p>We have sent you this email in response to your request to reset your password on family days app. After you reset your password to change to your own password.</p>
      <p>To set a password for your account,</p>
      <p>You can access the family days application with following credentials:</p></td>
    <td width="20%"></td>
  </tr>
    <tr>
      <td width="20%"></td>
      <td width="60%" style="border-right: 1px solid #d7d0d0;border-left: 1px solid #d7d0d0;background-color: white;">
        <table class="table" style="border-collapse: collapse;margin-left: 15px;margin-right: 15px;margin-top:15px;width: 90%;">
          <tbody>
           <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">Login Username </td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">{{ $userdetail['username'] }} / {{ $userdetail['email'] ? $userdetail['email']:'NA' }}</td>
            </tr>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">Password</td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">{{ $password }}</td>
            </tr>
          </tbody>
        </table>
        <p style="color: #6f5f5f;">* We recommend that you keep your password secure and not share it with anyone.</p>
      </td>
    <td width="20%"></td>
  </tr>
  <tr>
   <td width="20%"></td>
    <td width="60%" style="border-right: 1px solid #d7d0d0;border-left: 1px solid #d7d0d0;background-color: black;">
      <p style="text-align: center;color: white;">This message was sent to <span style="color: orange;">familydays@yahoo.com.</span> If this is not you please delete this email and send an email to support to report this error. This email has been generated with user knowledge by our system. Please login to change your preference if you no longer wish to receive this email. or contact support. We do not transmit nor do we ask for sensitive information over email. If any such information is transmitted or requested over email please report it to support. If you have any questions, contact us at <span style="color: orange;">support@familydays.com</span></p>
    <td width="20%"></td>
  </tr>
</table>
</body>
</html>

