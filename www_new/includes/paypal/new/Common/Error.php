<?php
function getDetailedExceptionMessage($ex) {
	if($ex instanceof PPConnectionException) {
		return "Error connecting to " . $ex->getUrl();
	} else if($ex instanceof PPConfigurationException) {
		return "Error at $ex->getLine() in $ex->getFile()";
	} else if($ex instanceof PPInvalidCredentialException || $x instanceof PPMissingCredentialException) {
		return $ex->errorMessage();
	}
	return "";
}
if (isset($ex) && $ex instanceof Exception) {
?>
<div id="wrapper">
<h3>Paypal Exception</h3>
<table>
<tr>
	<td>Type</td>
	<td><?php echo get_class($ex)?></td>
</tr>
<tr>
	<td>Message</td>
	<td><?php echo $ex->getMessage();?></td>
</tr>
<tr>
	<td>Detailed message</td>
	<td><?php echo getDetailedExceptionMessage($ex);?></td>
</tr>
</table>
</div>
<?php } ?>