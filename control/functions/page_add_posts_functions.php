<?php
/**
 * This handles the data processing for the Add Posts page.
 * @param safeCookie - The authentication cookie in use.
 **/
function engine_add_posts_page($safeCookie)
{
  if (isset($_POST['PostSubmit']))
  {
    if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
      echo '<div class="row"><p class="col-10 offset-1"><strong>Please fill in all fields.</strong></p></div>';
    else
      sub_engine_add_posts_SubmitOrDraft('Submit',$safeCookie);
  }
  else if (isset($_POST['PostDraft']))
  {
    if (!(isset($_POST['Title']) && isset($_POST['Content']) && isset($_POST['Tags']) && !empty($_POST['Title']) && !empty($_POST['Content']) && !empty($_POST['Tags'])))
      echo '<div class="row"><p class="col-10 offset-1"><strong>Please fill in all fields.</strong></p></div>';
    else
      sub_engine_add_posts_SubmitOrDraft('Draft',$safeCookie);
  }
  UI_add_edit_posts_page('Add',$safeCookie,0);
}

/**
 * This handles the Submit or Draft buttons present on the Add Posts page.
 * @param submitOrDraft - Whether this is a submit or a draft button.
 * @param safeCookie - The authentication cookie in use.
 **/
function sub_engine_add_posts_SubmitOrDraft($submitOrDraft,$safeCookie)
{
  $dBConnection = connect();
  $safeTitle = cleanString($dBConnection, $_POST['Title']);
  $safeNiceTitle = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $safeTitle)));
  $safePost = cleanHtmlString($dBConnection, '<div>' . nl2br($_POST['Content'],true) . '</div>');
  $safeTagList = cleanString($dBConnection, $_POST['Tags']);
  $safeTagArray = explode(',', $safeTagList);
  $safeTagOne = trim($safeTagArray[0]);// Use trim to remove spacing between commas from the final tags.
  $safeTagTwo = trim($safeTagArray[1]);
  $safeTagThree = trim($safeTagArray[2]);

  $dBQuery = "SELECT ID FROM `" . DBPREFIX . "_LoginTable` WHERE Cookie = '" . $safeCookie . "';";
  $returnQuery = mysqli_query($dBConnection,$dBQuery);
  while ($row = mysqli_fetch_array($returnQuery, MYSQLI_ASSOC))
    $returnedAuthorID = cleanHtmlString($dBConnection, $row['ID']);
  if ($submitOrDraft == 'Submit')
  {
    $dBQuery = "INSERT INTO `" . DBPREFIX . "_PostsTable` (AuthorID,Title,NiceTitle,TagOne,TagTwo,TagThree,Post,PostIsDraft) VALUES ('" . $returnedAuthorID . "','" . $safeTitle . "','" . $safeNiceTitle . "','" . $safeTagOne . "','" . $safeTagTwo . "','" . $safeTagThree . "','" . $safePost . "',0);";
    mysqli_query($dBConnection,$dBQuery);
    echo '<div class="row"><div class="col-10 offset-1"><p class="alert alert-success" role="alert">Posted!</p></div></div>
    <script>window.onload = function(){window.open("' . PROTOCOL . URL . '/' . $safeNiceTitle . '", "_blank");};</script>';
  }
  else if ($submitOrDraft == 'Draft')
  {
    $dBQuery = "INSERT INTO `" . DBPREFIX . "_PostsTable` (AuthorID,Title,NiceTitle,TagOne,TagTwo,TagThree,Post,PostIsDraft) VALUES ('" . $returnedAuthorID . "','" . $safeTitle . "','" . $safeNiceTitle . "','" . $safeTagOne . "','" . $safeTagTwo . "','" . $safeTagThree . "','" . $safePost . "',1);";
    mysqli_query($dBConnection,$dBQuery);
    echo '<div class="row"><div class="col-10 offset-1"><p class="alert alert-success" role="alert">Saved!</p></div></div>';
  }
  disconnect($dBConnection);
}
?>