<?php
session_start();

function outputHeader($title = "FIDK") {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            .grid-container {
                display: grid;
                grid-template-columns: 28% 1fr;
                grid-template-rows: auto 1fr;
                gap: 1px;
                height: 100vh;
                background: #f8f9fa;
            }
            
            .menu-area {
                grid-column: 1 / -1;
                background: white;
                padding: 0.5rem;
                border-bottom: 1px solid #dee2e6;
            }
            
            .folder-area {
                grid-column: 1;
                background: white;
                padding: 1rem;
                border-right: 1px solid #dee2e6;
                overflow-y: auto;
            }
            
            .content-area {
                grid-column: 2;
                display: grid;
                grid-template-columns: 70% 30%;
                grid-template-rows: 1fr auto;
                gap: 1px;
                background: white;
            }
            
            .preview-area {
                grid-column: 1;
                padding: 1rem;
                border-right: 1px solid #dee2e6;
                overflow-y: auto;
            }
            
            .tags-area {
                grid-column: 2;
                padding: 1rem;
                overflow-y: auto;
            }
            
            .info-area {
                grid-column: 2;
                height: 120px;
                padding: 1rem;
                border-top: 1px solid #dee2e6;
            }
            
            /* Additional styles */
            .folder-content ul {
                list-style: none;
                padding-left: 1rem;
            }
            
            .folder-content a {
                text-decoration: none;
                color: #0d6efd;
            }
            
            .folder-content a:hover {
                color: #0a58ca;
            }
            
            .alphabet-nav {
                background: #f8f9fa;
                padding: 0.5rem;
                border-radius: 4px;
                margin-bottom: 1rem;
            }
            
            .tag-cloud a {
                text-decoration: none;
                color: #0d6efd;
                padding: 0.25rem;
            }
            
            .tag-cloud a:hover {
                color: #0a58ca;
            }
        </style>
    </head>
    <body>
    <?php
}

function outputBrowseLayout() {
    ?>
    <div class="grid-container">
        <div class="menu-area">
            <?php 
            $included = true;
            include 'menu.inc.php'; 
            ?>
        </div>
        <div class="folder-area">
            <?php include 'folder_browse.php'; ?>
        </div>
        <div class="content-area">
            <div class="preview-area">
                <?php include 'preview_browse.php'; ?>
            </div>
            <div class="tags-area">
                <?php include 'tags_browse.php'; ?>
            </div>
            <div class="info-area">
                <?php include 'info_browse.php'; ?>
            </div>
        </div>
    </div>
    <?php
}

function outputManageLayout() {
    ?>
    <div class="grid-container">
        <div class="menu-area">
            <?php 
            $included = true;
            include 'menu.inc.php'; 
            ?>
        </div>
        <div class="folder-area">
            <?php include 'folder_manage.php'; ?>
        </div>
        <div class="content-area">
            <div class="preview-area">
                <?php include 'preview_manage.php'; ?>
            </div>
            <div class="tags-area">
                <?php include 'tags_manage.php'; ?>
            </div>
            <div class="info-area">
                <?php include 'info_manage.php'; ?>
            </div>
        </div>
    </div>
    <?php
}
?>
