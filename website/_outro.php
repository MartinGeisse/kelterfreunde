<?php

if (empty($including)) {
	die();
}
if (!empty($databaseConnection)) {
	$databaseConnection->close();
}
?>
</div>
</body>
</html>
