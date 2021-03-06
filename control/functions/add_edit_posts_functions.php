<?php
/**
 * This handles filling in the author details for the UI for the Add or Edit Posts page.
 * @param safeCookie - The authentication cookie in use.
 * @return String - the post author's details.
 **/
function sub_UI_add_edit_posts_FindAuthorDetails($safeCookie)
{
  $dBConnection = connect();
  $dBQuery = "SELECT Username FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    $returnedAuthor = cleanHtmlString($dBConnection, $row['Username']);
  disconnect($dBConnection);
  return '<p>Written by: ' . $returnedAuthor . ' on: ' . date("Y-m-d") . '.</p>';
}

/**
 * This handles the UI for the Add or Edit Posts page.
 * @param addEdit - Holds Add or Edit, depending on which we're doing.
 * @param safeCookie - The authentication cookie in use.
 * @param editPostID - The ID of the Post being edited.
 **/
function UI_add_edit_posts_page($addEdit,$safeCookie,$editPostID)
{
?><script>
function controlBoldFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ "<strong> BOLD TEXT HERE </strong>" +PostText );
}

function controlItalicFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ "<em> ITALIC TEXT HERE </em>" +PostText );
}

function controlUnderlineFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ '<span style="text-decoration:underline;"> UNDERLINED TEXT HERE </span>' +PostText );
}

function controlQuoteFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ '<blockquote> QUOTE HERE </blockquote>' +PostText );
}

function controlCodeFunc()
{
  var StartPosition = $('#Content').prop('selectionStart');
  var EndPosition = $('#Content').prop('selectionEnd');
  var ContentValue = $('#Content').val();
  var PreText = ContentValue.substring(0,  StartPosition );
  var PostText  = ContentValue.substring( EndPosition, ContentValue.length );
  $('#Content').val( PreText+ '<code> CODE HERE </code>' +PostText );
}
</script>
<div class="container-fluid">
  <div class="row">
    <form method="post" id="AccountChangeForm" class="form-horizontal col-10 offset-1">
      <fieldset class="form-group">
          <?php if ($addEdit == 'Add'){ ?>
        <legend>Add a Post:</legend>
          <?php } else if ($addEdit == 'Edit'){ ?>
        <legend>Edit a Post:</legend>
          <?php } ?>
        <div class="row">
          <div class="col-12">
            <input type="text" class="form-control" name="Title" id="Title" placeholder="Title" />
          </div>
        </div>
        <div class="row">
          <div class="col-12">
              <?php echo sub_UI_add_edit_posts_FindAuthorDetails ($safeCookie); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-8">
            <fieldset>
              <div>
                <a class="btn btn-light btn-sm" title="Bold" name="controlBold" onclick="controlBoldFunc()"><strong> B </strong></a>
                <a class="btn btn-light btn-sm" title="Italic" name="controlItalic" onclick="controlItalicFunc()"><em> I </em></a>
                <a class="btn btn-light btn-sm" title="Underlined" name="controlUnderline" onclick="controlUnderlineFunc()"><span style="text-decoration:underline;"> U </span></a>
                <a class="btn btn-light btn-sm" title="Block Quote" name="controlQuote" onclick="controlQuoteFunc()">&ldquo; &rdquo;</a>
                <a class="btn btn-light btn-sm" title="Code" name="controlCode" onclick="controlCodeFunc()"><code>&lt; &gt;</code></a>
              </div>
              <div style="height:0.4rem;"></div>
              <div>
                <textarea class="form-control" rows="12" name="Content" id="Content" ></textarea>
              </div>
              <br />
            </fieldset>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-control" style="height:315px;overflow-y:scroll;">  <?php engine_media_plugin(); ?></div>
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-12 col-sm-8">
            <input type="text" class="form-control" name="Tags" id="Tags" placeholder="3 comma separated Tags I.E: blog,post,hello" />
          </div>
        </div>
        <br />
          <?php if ($addEdit == 'Edit'){ ?><input type="hidden" name="Editor" id="Editor" value="  <?php echo $editPostID; ?>" />   <?php } ?>
        <div class="btn-group col-12">
          <input type="submit" class="btn btn-light col-4" name="PostSubmit" value="Write Post" />
          <input type="submit" class="btn btn-light col-4" name="PostDraft" value="Save Draft Post" />
          <input type="submit" class="btn btn-light col-4" name="PostCancel" value="Cancel Post" />
        </div>
      </fieldset>
    </form>
  </div>
</div><?php
}

/**
 * This handles fillling in the UI form on the Add or Edit Posts page if needed.
 * @param returnedPostID - The ID of the post returned from the database to work on.
 * @param returnedTitle - The Title of the post returned from the database to work on.
 * @param returnedPost - The post returned from the database to work on.
 * @param returnedTags - The tags from the post returned from the database to work on.
 **/
function sub_UI_add_edit_posts_JSFillForEdit($returnedPostID,$returnedTitle,$returnedPost,$returnedTags)
{
?><script>
  document.getElementById("Title").value = "<?php echo $returnedTitle; ?>";
  var content = `<?php echo substr($returnedPost,38,-39); ?>`;
  document.getElementById("Content").value = content;
  document.getElementById("Content").value = document.getElementById("Content").value.replace(/\[HTMLLEFTBRACKET\]/g, "<");// Replace safe tag entry for internal HTML.
  document.getElementById("Content").value = document.getElementById("Content").value.replace(/\[HTMLRIGHTBRACKET\]/g, ">"); // Replace safe tag closure for internal HTML.
  document.getElementById("Tags").value = "<?php echo $returnedTags; ?>";
</script><?php
}
?>