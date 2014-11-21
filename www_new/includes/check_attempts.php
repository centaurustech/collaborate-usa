<?php
/**
 * Function check_attempts
 * Description: Check Script usage attempts and returns false if attempts reach max allowed
 * Requirements: Sessions
 *
 * @author Raheel Hasan
 * @version 1.2
 *
 * @example
 * #/ Check Attempts
 * include_once('includes/check_attempts.php');
 * if(check_attempts(10)==false)
 * {
 *    return_back('resend-activation', true); exit;
 * }
 *
 * // return_back() function must call update_attempt_counts();
 *
**/

function check_attempts($allowed=10, $sess_msg_key='CUSA_MSG_GLOBAL')
{
    ###/ Check Attempts
    if(isset($_SESSION["au_wrongtry"]) && ($_SESSION["au_wrongtry"]>=$allowed))
    {
        $last_time = (int)$_SESSION['au_last_attempt'];
        $now = time();
        $stop_tm = rand(120, 220);

        if(($now-$last_time)>$stop_tm) // Reset after random 80 to 120 seconds
        {
            //$_SESSION["au_wrongtry"] = 0;
            //$_SESSION['au_last_attempt'] = 0;
            reset_attempt_counts();
        }
        else
        {
            $_SESSION[$sess_msg_key] = array(false, '<strong class="red-txt">Too Many Attempts!</strong><br />Please try again after a few minutes.');
            $_SESSION['au_last_attempt'] = time();
            return false;
        }
    }//end if attempt check....

    return true;

}//end func...


/**
 * Function update_attempt_counts
 * Description: Update the Attempt Counts. These counts are used by check_attempts function.
*/
function update_attempt_counts()
{
    #/ Update attempts count
    if(isset($_SESSION["au_wrongtry"])) $_SESSION["au_wrongtry"]++;
    else $_SESSION["au_wrongtry"] = 1;
    $_SESSION['au_last_attempt'] = time();

}//end func...


function reset_attempt_counts()
{
    $_SESSION["au_wrongtry"] = 0;
    $_SESSION['au_last_attempt'] = 0;

}//end func...
?>