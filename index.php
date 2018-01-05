<?php
require_once 'inc/config.php';

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>YouTube Uploader</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1 class="my-3">YouTube Uploader</h1>

        <?php
        // Check to ensure that the access token was successfully acquired.
        if ($client->getAccessToken()) :

            require_once 'inc/upload.php';

        else :

            // If the user hasn't authorized the app, initiate the OAuth flow
            $state = mt_rand();
            $client->setState($state);
            $_SESSION['state'] = $state;

            $authUrl = $client->createAuthUrl(); ?>

            <div id="authorize-first" class="alert alert-primary">
                <h3 class="alert-heading">Authorization Required</h3>
                <p>You need to <a href="<?php echo $authUrl; ?>" id="open-window">click here</a> to authorize access to your Google account before proceeding.</p>
            </div>

        <?php endif; ?>

        <form id="video-form" action="" method="post">
            <div class="form-group">
                <label for="video-path">Video Path: <small class="text-danger">*Required</small></label>
                <input class="form-control" type="url" id="video-path" name="video_path" required="required" placeholder="Ex.: https://domain.com/path/to/your/video.mp4">
            </div>
            <div class="form-group">
                <label for="video-title">Video Title:</label>
                <input class="form-control" type="text" id="video-title" name="video_title">
            </div>
            <div class="form-group">
                <label for="video-description">Video Description:</label>
                <textarea class="form-control" id="video-description" name="video_description"></textarea>
            </div>
            <div class="form-group">
                <label for="video-tags">Video Tags:</label>
                <input class="form-control" type="text" id="video-tags" name="video_tags">
                <small class="form-text text-muted">Separate with commas.</small>
            </div>
            <div class="form-group">
                <label for="video-status">Video Status:</label>
                <select class="form-control" id="video-status" name="video_status">
                    <option value="public">Public</option>
                    <option value="unlisted" selected="selected">Unlisted</option>
                    <option value="private">Private</option>
                </select>
            </div>
            <input id="video-submit" type="submit" class="btn btn-primary" value="Submit">
        </form>

        <script>
            var openWindow = document.getElementById('open-window'),
                authorizeFirst = document.getElementById('authorize-first'),
                videoForm = document.getElementById('video-form');

            if (openWindow) {
                openWindow.onclick = function(e) {
                    e.preventDefault();

                    var width = Math.min(window.innerWidth, 400),
                    height = Math.min(window.innerHeight, 600),
                    left = Math.max((window.innerWidth / 2) - (width / 2), 0),
                    top = Math.max((window.innerHeight / 2) - (height / 2), 0),
                    newWindow = window.open(this.href, '_blank', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);

                    var interval = setInterval(function() {
                        if (newWindow.closed) {
                            clearInterval(interval);
                            return false;
                        } else if (newWindow.location.href.indexOf('state') > 0 && newWindow.location.href.indexOf('code') > 0) {
                            clearInterval(interval);
                            newWindow.close();
                            authorizeFirst.remove();
                            return false;
                        }
                    }, 1000);
                };
            }

            if (videoForm) {
                videoForm.onsubmit = function(e) {
                    if (authorizeFirst.parentElement) {
                        alert('You have to authorize access to your Google account before proceeding.');
                        return false;
                    }
                }
            }
        </script>

    </div>
</body>
</html>
