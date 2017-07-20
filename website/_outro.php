<?php

if (empty($including)) {
	die();
}
if (!empty($databaseConnection)) {
	$databaseConnection->close();
}
?>
</div>
<script type="text/javascript">
	window.parent.postMessage('iframe-embedding:' + document.getElementById('content-container').scrollHeight,'*');
</script>
</body>
</html>
