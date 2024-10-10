<?php namespace ProcessWire;
/**
 * @var ProcessWire $wire
 * @var FrontendAutoReload $far
 */
?>
<script>
    console.log(
        'FrontendAutoReload started.\n',
        'Excluded directories: <?= json_encode($far->getExcludedDirectories()) ?>\n',
        'Excluded extensions: <?= json_encode($far->getExcludedExtensions()) ?>\n',
        'Polling interval: <?= $far->getInterval() ?> seconds\n'
    );

    farPoll('<?=$far->getEndpointUrl()?>', <?= $far->getInterval() * 1000 ?>);

    function farPoll(url, interval = 5000) {
        // Define the function to make a request to the URL
        const makeRequest = () => {
            fetch(url,{
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-type': 'application/json' },
                body: JSON.stringify(<?=json_encode($far->getConfig())?>)
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json(); // Assuming the response is JSON
                })
                .then(timestamp => {
                    farProcessTimestamp(timestamp);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        };

        // Immediately make the first request, then set up polling
        makeRequest();

        // Use setInterval to repeat the request every X milliseconds
        const intervalId = setInterval(makeRequest, interval);

        // Optionally, you could return a method to stop the polling if needed
        return () => clearInterval(intervalId);
    }


    function farProcessTimestamp(timestamp) {
        const previousTimestamp = localStorage.getItem("farTimestamp");

        // if we don't have a previous timestamp, store the current one and return
        if(previousTimestamp === null) {
            localStorage.setItem("farTimestamp", timestamp);
            return;
        }

        // if the previous timestamp is less than the current one, reload the page
        if(previousTimestamp < timestamp) {
            localStorage.setItem("farTimestamp", timestamp);
            location.reload();
        }
    }

</script>
