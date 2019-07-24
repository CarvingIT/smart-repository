<form name="upload_resume_form" action="upload_resume" method="post" enctype="multipart/form-data">
@csrf()
<input type="file" name="resume"><br />
<button type="submit">Upload Resume</button>
</form>
