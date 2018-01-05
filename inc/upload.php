<?php

if (!empty($_POST['video_path'])) {

    try{

        // REPLACE this value with the path to the file you are uploading.
        $videoPath = $_POST['video_path'];

        // Create a snippet with title, description, tags and category ID
        // Create an asset resource and set its snippet metadata and type.
        // This example sets the video's title, description, keyword tags, and
        // video category.
        $snippet = new Google_Service_YouTube_VideoSnippet();

        if (!empty($_POST['video_title'])) {
            $snippet->setTitle($_POST['video_title']);
        }

        if (!empty($_POST['video_description'])) {
            $snippet->setDescription($_POST['video_description']);
        }

        if (!empty($_POST['video_tags'])) {
            $video_tags = array_filter(array_map('trim', explode(',', $_POST['video_tags'])));
            $snippet->setTags($video_tags);
        }

        // Numeric video category. See
        // https://developers.google.com/youtube/v3/docs/videoCategories/list
        // $snippet->setCategoryId("22");

        // Set the video's status to "public". Valid statuses are "public",
        // "private" and "unlisted".
        $status = new Google_Service_YouTube_VideoStatus();

        if (!empty($_POST['video_status']) && in_array($_POST['video_status'], array('public', 'private', 'unlisted'))) {
            $status->privacyStatus = $_POST['video_status'];
        }

        // Associate the snippet and status objects with a new video resource.
        $video = new Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        // Specify the size of each chunk of data, in bytes. Set a higher value for
        // reliable connection as fewer chunks lead to faster uploads. Set a lower
        // value for better recovery on less reliable connections.
        $chunkSizeBytes = 1 * 1024 * 1024;

        // Setting the defer flag to true tells the client to return a request which can be called
        // with ->execute(); instead of making the API call immediately.
        $client->setDefer(true);

        // Create a request for the API's videos.insert method to create and upload the video.
        $insertRequest = $youtube->videos->insert('status,snippet', $video);
        // Create a MediaFileUpload object for resumable uploads.
        $media = new Google_Http_MediaFileUpload(
            $client,
            $insertRequest,
            'video/*',
            null,
            true,
            $chunkSizeBytes
        );
        // $media->setFileSize(filesize($videoPath));
        $headers = array_change_key_case(get_headers($videoPath, true));
        $media->setFileSize(intval($headers['content-length']));

        // Read the media file and upload it chunk by chunk.
        $status = false;
        $handle = fopen($videoPath, 'rb');
        while (!$status && !feof($handle)) {
            $chunk = stream_get_contents($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);

        // If you want to make other calls after the file upload, set setDefer back to false
        $client->setDefer(false);

        $video_url = 'https://www.youtube.com/watch?v=' . $status['id'];
        ?>

        <div class="alert alert-success">
            <h3 class="alert-heading">Video Uploaded</h3>
            <p><a href="<?php echo $video_url; ?>" class="alert-link"><?php echo $video_url; ?></a></p>
        </div>

        <?php
    } catch (Google_Service_Exception $e) {
        ?>

        <div class="alert alert-danger">
            <p>A service error occurred: <code><?php echo htmlspecialchars($e->getMessage()); ?></code></p>
        </div>

        <?php
    } catch (Google_Exception $e) {
        ?>

        <div class="alert alert-danger">
            <p>An client error occurred: <code><?php echo htmlspecialchars($e->getMessage()); ?></code></p>
        </div>

        <?php
    }

    $_SESSION['token'] = $client->getAccessToken();
}
