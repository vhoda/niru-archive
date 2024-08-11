<?php include 'b.php'; ?>
<?php include 'nav.php'; ?>
<title>Vhoda's Archive</title>
<div class="container mt-5">
    <div class="row">
        <h4>Files</h4>
    </div>
    <div class="row py-4">
        <div class="container">
            <?php
            $dir = __DIR__ . '/content';
            $extensions = ['mp4', 'mp3', 'wav', 'webm'];

            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    $files = [];

                    while (($file = readdir($dh)) !== false) {
                        $file_extension = pathinfo($file, PATHINFO_EXTENSION);

                        if (in_array($file_extension, $extensions) && $file !== '.' && $file !== '..') {
                            $file_path = $dir . '/' . $file;
                            $files[] = [
                                'name' => $file,
                                'path' => $file_path,
                                'mod_time' => filemtime($file_path),
                                'extension' => $file_extension
                            ];
                        }
                    }
                    closedir($dh);

                    // Listed for the newest first
                    usort($files, function ($a, $b) {
                        return $b['mod_time'] - $a['mod_time'];
                    });

                    if (count($files) > 0) {
                        foreach ($files as $file_data) {
                            $file = $file_data['name'];
                            $file_extension = $file_data['extension'];
                            $file_url = "content/" . urlencode($file);
                            $file_mod_time = date("d-m-Y H:i:s", $file_data['mod_time']);

                            // Calculate if the file is "New"
                            $is_new = (time() - $file_data['mod_time']) <= 7 * 24 * 60 * 60;

                            echo '<div class="cardd col-12 mb-2 d-flex align-items-center">';
                            if ($file_extension === 'mp4') {
                                echo '<div class="col-2 text-center"><i class="fas fa-file-video fa-2x"></i></div>';
                            } elseif ($file_extension === 'mp3' || $file_extension === 'wav') {
                                echo '<div class="col-2 text-center"><i class="fas fa-file-audio fa-2x"></i></div>';
                            } elseif ($file_extension === 'webm') {
                                echo '<div class="col-2 text-center"><i class="fas fa-file-video fa-2x"></i></div>';
                            }
                            echo '<div class="col-8">';
                            echo '<span class="text-wrap">' . htmlspecialchars($file) . '</span>';
                            // Add "New" badge
                            if ($is_new) {
                                echo ' <span class="badge text-bg-secondary p-1 ms-1">New</span>';
                            }
                            echo '<div class="text-muted" style="font-size: 0.9em;">' . $file_mod_time . '</div>';
                            echo '</div>';
                            // desktop buttons
                            echo '<div class="col-2 text-center">';
                            echo '<button class="btn btn-secondary escritorio rounded-pill" onclick="shareItem(\'' . $file_extension . '\', \'' . $file . '\')"><i class="fas fa-share-alt px-1"></i> Share</button>';
                            echo '<button class="btn btn-discovery escritorio rounded-pill load-media ms-2" data-file-url="' . $file_url . '" data-file-type="' . $file_extension . '" onclick="playMedia(\'' . $file_extension . '\', \'' . $file . '\')"><i class="fas fa-play px-1"></i> Play</button>';
                            echo '</div>';
                            echo '</div>';
                            // mobile buttons
                            echo '<div class="d-grid gap-2 mb-3">';
                            echo '<button class="btn btn-discovery movil rounded-pill load-media" data-file-url="' . $file_url . '" data-file-type="' . $file_extension . '" onclick="playMedia(\'' . $file_extension . '\', \'' . $file . '\')"><i class="fas fa-play px-1"></i> Play</button>';
                            echo '<button class="btn btn-secondary movil rounded-pill" onclick="shareItem(\'' . $file_extension . '\', \'' . $file . '\')"><i class="fas fa-share-alt px-1"></i> Share</button>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col-12 text-center text-danger mt-4">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                    <p class="mt-2">The directory is empty.</p>
                  </div>';
                    }
                } else {
                    echo '<div class="col-12 text-center text-danger mt-4">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
                <p class="mt-2">Error to open this <strong>' . htmlspecialchars($dir) . '</strong>.</p>
              </div>';
                }
            } else {
                echo '<div class="col-12 text-center text-danger mt-4">
            <i class="fas fa-exclamation-triangle fa-3x"></i>
            <p class="mt-2">The directory <strong>' . htmlspecialchars($dir) . '</strong> doesnt exists.</p>
          </div>';
            }
            ?>
        </div>
    </div>


    <!-- Modal player -->
    <div class="modal fade" id="modalPlayer" tabindex="-1" aria-labelledby="modalPlayerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mb-0" id="modalPlayerLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="media-container">
                    <!-- Player -->
                </div>
                <div class="modal-footer">
                    <?php
                    // Share button
                    if (isset($file_extension) && isset($file)) {
                        echo '<button class="btn btn-secondary rounded-pill" onclick="shareItem(\'' . $file_extension . '\', \'' . $file . '\')"><i class="fas fa-share-alt px-1"></i> Share</button>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {


            // Función for copy to clipboard and share a especific file
            window.shareItem = function(type, filename) {
                var baseUrl = window.location.href.split('?')[0];
                var shareUrl = baseUrl + '?file=' + encodeURIComponent(filename) + '&type=' + encodeURIComponent(type);

                // Fallback for HTTP
                fallbackCopyToClipboard(shareUrl);
            };

            function fallbackCopyToClipboard(text) {
                var textarea = $('<textarea>').val(text).appendTo('body').select();
                try {
                    document.execCommand('copy');
                    showToast('Copied to your Clipboard! :)', 'bg-success');
                } catch (err) {
                    showToast('Unable to copy to clipboard.', 'bg-danger');
                } finally {
                    textarea.remove();
                }
            }

            function showToast(message, bgClass) {
                var toastHTML = '<div class="toast align-items-center text-white ' + bgClass + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">' +
                    '<div class="d-flex">' +
                    '<div class="toast-body">' + message + '</div>' +
                    '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                    '</div>' +
                    '</div>';

                var toastContainer = $('#toast-container');
                if (toastContainer.length === 0) {
                    toastContainer = $('<div id="toast-container" class="position-fixed bottom-0 end-0 p-3"></div>').appendTo('body');
                }
                toastContainer.html(toastHTML);
                var toast = new bootstrap.Toast(toastContainer.find('.toast'));
                toast.show();
            }

            function clearUrlParams() {
                var url = new URL(window.location);
                url.search = "";
                window.history.replaceState({}, document.title, url.toString());
            }

            window.playMedia = function(type, filename) {
                var fileUrl = "content/" + encodeURIComponent(filename);
                var mediaElement = '';

                if (type === 'mp4') {
                    mediaElement = '<video id="player" controls preload="auto" width="100%">' +
                        '<source src="' + fileUrl + '" type="video/mp4">' +
                        'Tu navegador no soporta el elemento de video.' +
                        '</video>';
                } else if (type === 'mp3') {
                    mediaElement = '<audio id="player" controls preload="auto">' +
                        '<source src="' + fileUrl + '" type="audio/mpeg">' +
                        'Tu navegador no soporta el elemento de audio.' +
                        '</audio>';
                } else if (type === 'wav') {
                    mediaElement = '<audio id="player" controls preload="auto">' +
                        '<source src="' + fileUrl + '" type="audio/wav">' +
                        'Tu navegador no soporta el elemento de audio.' +
                        '</audio>';
                } else if (type === 'webm') {
                    mediaElement = '<video id="player" controls preload="auto" width="100%">' +
                        '<source src="' + fileUrl + '" type="video/webm">' +
                        'Tu navegador no soporta el elemento de video.' +
                        '</video>';
                }

                $('#media-container').html(mediaElement);
                $('#modalPlayerLabel').text(filename);

                var modalPlayer = new bootstrap.Modal(document.getElementById('modalPlayer'));
                modalPlayer.show();

                // funtion if the modal closes, the player stops.
                $('#modalPlayer').on('hidden.bs.modal', function() {
                    $('#player').each(function() {
                        this.pause();
                    });
                });
            };

            // Open to modal if you enter with a link
            const urlParams = new URLSearchParams(window.location.search);
            const fileParam = urlParams.get('file');
            const typeParam = urlParams.get('type');

            if (fileParam && typeParam) {
                playMedia(typeParam, fileParam);
                clearUrlParams();
            }
        });
    </script>
</div>
