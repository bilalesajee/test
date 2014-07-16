<?php



function email($To, $Subject, $Body)
{
    require_once("mail.inc.php");
    $mail = new e_phpmailer();

    $mail->Subject = $Subject;

    $mail->MsgHTML($Body);


    if (!is_array($To))
    {
        $emails[] = $To;
    }
    else
    {
        $emails = $To;
    }

    foreach ($emails as $t)
    {

        if ($mail->ValidateAddress($t))
        {
            $send = true;
            $mail->AddAddress($t);
        }
    }


    if ($send)
    {
        $mail->Send();
    }
}