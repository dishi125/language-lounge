@extends('emails.layout.emailapp')
@section('content')
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tr>
        <td class="p30-15" style="padding: 50px  30px 0px 30px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="text2 pb15"
                        style="color:#585858; font-family:'Montserrat', sans-serif; font-size:14px; line-height:20px; padding-bottom: 10px;">
                        Hello {!! $data['name'] !!},<br>
                        <br>
                        <p>Please check your one time code for the forgot password.</p><br>
                        <p>Email : {!! $data['email'] !!}</p>
                        <p>One Time Code : <b>{!! $data['code'] !!}</b></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection