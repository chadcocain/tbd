<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Testing Upload</title>
<script type="text/javascript" src="http://www.twobeerdudes.com/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="http://www.twobeerdudes.com/js/jquery-ui-1.8.16.custom.min.js"></script>
</head>
<body>

<div class="fileupload">
    <form action="http://www.twobeerdudes.com/tiwama/upload_data" method="post" enctype="multipart/form-data">
        <div class="fileupload-buttonbar">
            <label class="fileinput-button">
                <span>Add files...</span>
                <input type="file" name="files[]" multiple="multiple">
            </label>
            <button type="submit" class="start">Start upload</button>
        </div>
    </form>
    <div class="fileupload-content">
        <table class="files"></table>
        <div class="fileupload-progressbar"></div>
    </div>
</div>

<div class="fileupload">
    <form action="http://www.twobeerdudes.com/tiwama/upload_data" method="post" enctype="multipart/form-data">
        <div class="fileupload-buttonbar">
            <label class="fileinput-button">
                <span>Add files...</span>
                <input type="file" name="files[]" multiple="multiple">
            </label>
            <button type="submit" class="start">Start upload</button>
        </div>
    </form>
    <div class="fileupload-content">
        <table class="files"></table>
        <div class="fileupload-progressbar"></div>
    </div>
</div>

</body>
</html>