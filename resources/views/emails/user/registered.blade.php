<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>New register in family days</title>
    <style type="text/css">
      body{
         background-color: #e8e4e4;
         font-family: Arial, Helvetica, sans-serif;
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
      <p style="color: #ca6710;font-weight: 500;font-size: 16px;">New register in family days</p>
      <p><b>Hello {{ $userdetail['full_name'] }},</b></p>
      <p>Your account has been registered on family days</p>
      <p>You can sign in to your account by using your this usernamee {{ $userdetail['username'] }} or email {{ $userdetail['email'] ? $userdetail['email']:'NA' }} and password set by you,</p>
     </td>
    <td width="20%"></td>
  </tr>
    <tr>
      <td width="20%"></td>
      <td width="60%" style="border-right: 1px solid #d7d0d0;border-left: 1px solid #d7d0d0;background-color: white;">
        <table class="table" style="border-collapse: collapse;margin-left: 15px;margin-right: 15px;margin-top:15px;width: 90%;">
          <tbody>
           <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">Family Name </td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">{{ $userdetail['use_family_name'] }}</td>
            </tr>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">Username</td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">{{ $userdetail['username'] }}</td>
            </tr>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">Fullname</td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">{{ $userdetail['full_name'] }}</td>
            </tr>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">Email</td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">{{ $userdetail['email'] ? $userdetail['email']:'NA' }}</td>
            </tr>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">Phone No.</td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">{{ $userdetail['use_phone_no'] ? $userdetail['use_phone_no']:'NA' }}</td>
            </tr>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">Birth Date</td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">@if($userdetail['use_dob']) {{ date('M-d-Y', strtotime(str_replace('/', '-', $userdetail['use_dob']))) }} @else {{ "" }} @endif</td>
            </tr>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">User Type</td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">
              @if($userdetail['role'] == 2) {{ "Father" }} 
              @elseif($userdetail['role'] == 3) {{ "Mother" }} 
              @elseif($userdetail['role'] == 4) {{ "Son" }} 
              @elseif($userdetail['role'] == 5) {{ "Daughter" }} 
              @endif</td>
            </tr>
            <tr>
              <td style="border:1px solid #ccc5c5;padding: 8px;width: 25%;font-weight: 600;">Join Date </td>
              <td style="border:1px solid #ccc5c5;padding: 8px;">@if($userdetail['created_at']) {{ date('M-d-Y', strtotime(str_replace('/', '-', $userdetail['created_at']))) }} @else {{ "" }} @endif</td>
            </tr>
          </tbody>
        </table>
        <p style="color: #6f5f5f;">* Thanks! For new register in family days application.</p>
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