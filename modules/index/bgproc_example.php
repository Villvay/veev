
<h2>Example Background Process</h2>

<input type="button" value="Start a Process" disabled />

<pre id="status"></pre>

<script>
var jobId = '<?php echo isset($_SESSION['background-job-id']) ? $_SESSION['background-job-id'] : 'none'; ?>';
var dispStatus = q('pre#status')[0];
var startBtn = q('input[type="button"]')[0];
var invalidKey = '{\n    "error": "invalid-key"\n}';
function updateStatus(){
	arc.ajax('<?php echo BASE_URL; ?>bgproc_example/status/'+jobId,
		{
			method: GET,
			callback: function(data){
				startBtn.disabled = (data.responseText != invalidKey);
				dispStatus.innerHTML = (data.responseText == invalidKey ? 'Not Running' : data.responseText);
			}
		}
	);
	setTimeout(updateStatus, 990);
}
updateStatus();

startBtn.onclick = function(){
	arc.ajax('<?php echo BASE_URL; ?>bgproc_example/start',
		{
			method: GET,
			callback: function(data){
				startBtn.disabled = (data.responseText != '{"error":"invalid key"}');
				data = eval('('+data.responseText+')');
				jobId = data.jobId;
			}
		}
	);
	updateStatus();
}
</script>