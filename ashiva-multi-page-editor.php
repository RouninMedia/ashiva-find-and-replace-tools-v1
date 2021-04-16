<?php


  //*****************//
 // ERROR REPORTING //
//*****************//

error_reporting(E_ALL);
ini_set('display_errors', 1);


  //***************//
 // GET ALL PAGES //
//***************//

function Get_Full_Page_List($Page_List_Array) {

  for ($i = 0; $i < count($Page_List_Array); $i++) {

    $Skip_Folders = ['.', '..'];

    $Subfolders = scandir($Page_List_Array[$i]);

    for ($j = 0; $j < count($Subfolders); $j++) {

      if (in_array($Subfolders[$j], $Skip_Folders)) continue;
      if (!is_dir($Page_List_Array[$i].'/'.$Subfolders[$j])) continue;

      $Page_List_Array[] = $Page_List_Array[$i].'/'.$Subfolders[$j];
    }

    $Page_List_Array = array_unique($Page_List_Array);
  }

  return $Page_List_Array;
}


  //******************//
 // REFINE PAGE LIST //
//******************//

function Refine_Page_List($Full_Page_List_Array) {

  $Refined_Page_List_Array = [];

  for ($i = 0; $i < count($Full_Page_List_Array); $i++) {

    if (file_exists($Full_Page_List_Array[$i].'/index.php')) {

      $Find_Phrase = $_GET['findPhrase'];
      $Page_Text = file_get_contents($Full_Page_List_Array[$i].'/index.php');

      if (isset($_GET['caseSensitive']) && ($_GET['caseSensitive'] === 'false')) {

        $Find_Phrase = strtolower($Find_Phrase);
        $Page_Text = strtolower($Page_Text);
      }

      if (strpos($Page_Text, $Find_Phrase)) {

        $Refined_Page_List_Array[] = $Full_Page_List_Array[$i];
      }
    }
  }

  return $Refined_Page_List_Array;
}


    
  //****************//
 // EDIT PAGES NOW //
//****************//

if ((isset($_GET['pagesTargeted'])) && (isset($_GET['editPagesNow'])) && ($_GET['editPagesNow'] === 'true')) {

  $Document_Root = $_SERVER['DOCUMENT_ROOT'];

  $Find_Phrase = $_GET['findPhrase'];
  $Replace_Phrase = $_GET['replacePhrase'];
  $Case_Sensitive = ($_GET['caseSensitive'] === 'true') ? TRUE : FALSE;
  $Pages_To_Edit = json_decode(rawurldecode($_GET['pagesTargeted']), TRUE);

  for ($i = 0; $i < count($Pages_To_Edit); $i++) {

    $Current_Page = file_get_contents($Document_Root.'/.assets/content/pages/'.substr($Pages_To_Edit[$i], 1));

    $Replace_Phrase = ($Replace_Phrase === '␀') ? '' : $Replace_Phrase;

    switch ($Case_Sensitive) {

      case (TRUE) : $New_Page = str_replace($Find_Phrase, $Replace_Phrase, $Current_Page); break;
      case (FALSE) : $New_Page = preg_replace('/'.$Find_Phrase.'/i', $Replace_Phrase, $Current_Page); break;
    }
    
    $fp = fopen($Document_Root.'/.assets/content/pages/'.substr($Pages_To_Edit[$i], 1), 'w');
    fwrite($fp, $New_Page);
    fclose($fp);
  }

  $Protocol = 'https://';
  $Domain = $_SERVER['HTTP_HOST'];
  $Path = $_SERVER['SCRIPT_NAME'];

  $Query_String = explode('&editPagesNow', $_SERVER['QUERY_STRING'])[0];
  $Query_String = '?'.str_replace('pagesTargeted', 'pagesEdited', $Query_String);

  header('Location: '.$Protocol.$Domain.$Path.$Query_String);
}


  //******************//
 // BUILD PAGE LISTS //
//******************//

if (isset($_GET['formAction'])) {

  if ($_GET['formAction'] === 'confirm-pages') {

    if ((!isset($_GET['pagesEdited'])) || ($_GET['pagesEdited'] === '␀')) {

      $_GET['formAction'] = 'search-pages';
    }
  }

  if ($_GET['formAction'] === 'search-pages') {

    $Start_Folder = $_SERVER['DOCUMENT_ROOT'].'/.assets/content/pages';

    $Full_Page_List_Array = Get_Full_Page_List([$Start_Folder]);

    $Refined_Page_List_Array = Refine_Page_List($Full_Page_List_Array);
  }

  elseif (($_GET['formAction'] === 'confirm-pages') && ($_GET['pagesEdited'] !== '␀')) {

    $Pages_Edited_Array = json_decode(rawurldecode($_GET['pagesEdited']), TRUE);
    $Pages_Untargeted_Array = json_decode(rawurldecode($_GET['pagesUntargeted']), TRUE);

    $Start_Folder = $_SERVER['DOCUMENT_ROOT'].'/.assets/content/pages';

    $Full_Page_List_Array = Get_Full_Page_List([$Start_Folder]);

    $Refined_Page_List_Array = Refine_Page_List($Full_Page_List_Array);
  }
}


  //***************//
 // SET VARIABLES //
//***************//

$Form_Action = (isset($_GET['formAction'])) ? $_GET['formAction'] : 'initialise';
$Find_Phrase = (isset($_GET['findPhrase'])) ? $_GET['findPhrase'] : '␀';
$Case_Sensitive = (isset($_GET['caseSensitive'])) ? $_GET['caseSensitive'] : 'true';
$Replace_Phrase = (isset($_GET['replacePhrase'])) ? $_GET['replacePhrase'] : '␀';
$Replace_Activated = (isset($_GET['replaceActivated'])) ? $_GET['replaceActivated'] : 'false';

if ((isset($Refined_Page_List_Array)) && ($_GET['pagesFound'] === '␀')) {

  $Pages_Found = (count($Refined_Page_List_Array) > 0) ? 'true' : 'false';

  $Protocol = 'https://';
  $Domain = $_SERVER['HTTP_HOST'];
  $Path = $_SERVER['SCRIPT_NAME'];
  $Query_String = ($_SERVER['QUERY_STRING'] === '') ? '' : '?'. str_replace('pagesFound=%E2%90%80', 'pagesFound='.$Pages_Found, $_SERVER['QUERY_STRING']);

  header('Location: '.$Protocol.$Domain.$Path.$Query_String);
}

$Pages_Found = (isset($_GET['pagesFound'])) ? $_GET['pagesFound'] : '␀';
$Pages_Edited = ((isset($_GET['pagesEdited'])) && ($_GET['pagesEdited'] !== '␀')) ? rawurlencode($_GET['pagesEdited']) : '␀';
$Pages_Untargeted = ((isset($_GET['pagesUntargeted'])) && ($_GET['pagesUntargeted'] !== '␀')) ? rawurlencode($_GET['pagesUntargeted']) : '␀';



  //**************************//
 // ASHIVA MULTI-PAGE EDITOR //
//**************************//


echo '<!DOCTYPE html>

<html lang="en-GB">
<head>
<meta charset="utf-8">
<title>Ashiva MultiPage Editor</title>
<meta name="viewport" content="initial-scale=1.0" />
<style>

:root {
  --text-color-main: rgb(255, 255, 255);
  --height-header: 60px;
}

body {
  margin: 0;
  padding: var(--height-header) 0 0;
  color: var(--text-color-main);
  font-family: sans-serif;
  background-color: rgb(191, 0, 0);
  min-height: 100vh;
}

header,
h1 {
  position: fixed;
  display: block;
  top: 0;
  left: 0;
  z-index: 12;
  width: 100%;
  height: var(--height-header);
  margin: 0;
  line-height: var(--height-header);
  text-align: center;
}

header {
  background-color: rgb(131, 0, 0);
  border-bottom: 1px solid rgb(67, 0, 0);
  box-shadow: 0 0 12px rgb(67, 0, 0);
  box-sizing: border-box;
}

h2 {
  padding: 12px 0;
  font-size: 14px;
}

.multiPageEditorForm {
  position: absolute;
  top: 72px;
  left: 0;
  z-index: 6;
  width: 800px;
  margin: 0 calc((100vw - 800px) / 2);
}

.multiPageEditorFieldset {
  background-color: rgb(207, 0, 0);
  border: 1px solid rgb(179, 0, 0);
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.6);
}

.multiPageEditorLabel {
  display: block;
  margin: 12px 3px;
}

.multiPageEditorInput {
  width: calc(100% - 12px);
  padding: 6px;
}

.multiPageEditorInput::placeholder {
  color: rgba(0, 0, 0, 0.5);
}

.multiPageEditorLabel.\--find {
  margin-top: 36px;
}

.multiPageEditorInput.\--find:invalid {
  background-color: rgb(231, 128, 128);
  border: 0;
  outline: 1px solid rgb(255, 255, 0);
}

.multiPageEditorLabel.\--replace {
  cursor: pointer;
}

.multiPageEditorInput.\--replace {
  opacity: 0.5;
  pointer-events: none;
}

.multiPageEditorForm[data-replace-activated="true"] .multiPageEditorInput.\--replace {
  opacity: 1;
  pointer-events: auto;
}

.multiPageEditorLegend {
  font-weight: 900;
  font-size: 12px;
  text-transform: uppercase;
}

.multiPageEditorButtonGroup.\--edit {
  margin-top: 48px;
}

.searching,
.listening,
.multiPageEditorForm[data-pages-edited^="%5B"] .pagesSearched,
.pagesEdited,
.pagesUntargeted,
.multiPageEditorPagesEdited,
.multiPageEditorPagesUntargeted,
.pageList.\--toEdit,
.multiPageEditorForm[data-replace-activated="true"] .multiPageEditorLegendSpan.\--find,
.multiPageEditorLegendSpan.\--replace,
.multiPageEditorButtonSpan,
.multiPageEditorButton.\--confirm,
.multiPageEditorNotFound,
.multiPageEditorListConfirmed,
.multiPageEditorButtonGroup,
.pageListCheckbox,
.multiPageEditorHeading,
.multiPageEditorForm[data-form-action="confirm-pages"] .multiPageEditorFieldset {
  display: none;
}

.multiPageEditorForm[data-form-action="input-text"][data-replace-activated="true"] .multiPageEditorHeading.\--replace,
.multiPageEditorForm[data-form-action="search-pages"][data-replace-activated="true"] .multiPageEditorHeading.\--replace,
.multiPageEditorForm[data-form-action="input-text"][data-replace-activated="true"][data-replace-phrase="␀"] .multiPageEditorHeading.\--remove,
.multiPageEditorForm[data-form-action="search-pages"][data-replace-activated="true"][data-replace-phrase="␀"] .multiPageEditorHeading.\--remove,
.multiPageEditorForm[data-form-action="input-text"][data-replace-activated="false"] .multiPageEditorHeading.\--find,
.multiPageEditorForm[data-form-action="search-pages"][data-replace-activated="false"] .multiPageEditorHeading.\--find,
.multiPageEditorForm[data-form-action="search-pages"][data-pages-found="false"] .multiPageEditorNotFound,
.multiPageEditorForm[data-form-action="search-pages"][data-pages-found="true"] .pageList.\--toEdit,
.multiPageEditorForm[data-form-action="search-pages"][data-pages-found="true"][data-replace-activated="true"] .multiPageEditorButton.\--confirm,
.multiPageEditorForm[data-form-action="confirm-pages"][data-pages-edited^="%5B"] .pagesEdited,
.multiPageEditorForm[data-form-action="confirm-pages"][data-pages-edited^="%5B"] .pagesUntargeted,
.multiPageEditorForm[data-form-action="confirm-pages"][data-pages-edited="␀"] .multiPageEditorListConfirmed.\--replace,
.multiPageEditorForm[data-form-action="confirm-pages"][data-pages-edited="␀"][data-replace-phrase="␀"] .multiPageEditorListConfirmed.\--remove,
.multiPageEditorForm[data-form-action="confirm-pages"] .pageList.\--toEdit,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase][data-pages-edited^="%5B"] .multiPageEditorPagesEdited.\--replaced,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase="␀"][data-pages-edited^="%5B"] .multiPageEditorPagesEdited.\--removed,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase][data-pages-untargeted^="%5B"] .multiPageEditorPagesUntargeted.\--replaced,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase="␀"][data-pages-untargeted^="%5B"] .multiPageEditorPagesUntargeted.\--removed {
  display: block;
}

.multiPageEditorForm[data-form-action="search-pages"][data-pages-found="true"][data-replace-activated="true"] .multiPageEditorButtonGroup.\--list,
.multiPageEditorForm[data-form-action="confirm-pages"] .multiPageEditorButtonGroup.\--edit {
  display: flex;
  justify-content: center;
}

.multiPageEditorForm[data-form-action="input-text"] .listening,
.multiPageEditorForm[data-form-action="search-pages"] .searching,
.multiPageEditorForm[data-replace-activated="true"] .multiPageEditorLegendSpan.\--replace,
.multiPageEditorForm[data-replace-activated="true"] .multiPageEditorButtonSpan,
.multiPageEditorForm[data-form-action="search-pages"][data-replace-activated="true"] .pageListCheckbox {
  display: inline;
}

.multiPageEditorForm[data-replace-activated="true"][data-replace-phrase="␀"] .multiPageEditorHeading.\--replace,
.multiPageEditorForm[data-form-action="confirm-pages"] .multiPageEditorHeading.\--replace,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase="␀"] .multiPageEditorHeading.\--remove,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase="␀"] .multiPageEditorListConfirmed.\--replace,
.multiPageEditorForm[data-form-action="confirm-pages"][data-pages-edited="␀"] .multiPageEditorButton.\--edit.\--reinitialise,
.multiPageEditorForm[data-form-action="confirm-pages"][data-pages-edited^="%5B"] .multiPageEditorButton.\--edit.\--back,
.multiPageEditorForm[data-form-action="confirm-pages"][data-pages-edited^="%5B"] .multiPageEditorButton.\--edit.\--editNow,
.multiPageEditorForm[data-form-action="confirm-pages"][data-pages-edited="␀"] .pagesEdited,
.multiPageEditorForm[data-form-action="confirm-pages"][data-pages-edited="␀"] .multiPageEditorListConfirmed,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase="␀"][data-pages-edited^="%5B"] .multiPageEditorPagesEdited.\--replaced,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase="␀"][data-pages-edited^="%5B"] .multiPageEditorPagesUntargeted.\--replaced,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase][data-pages-untargeted="%5B%5D"] .multiPageEditorPagesUntargeted.\--replaced,
.multiPageEditorForm[data-form-action="confirm-pages"][data-replace-phrase][data-pages-untargeted="%5B%5D"] .multiPageEditorPagesUntargeted.\--removed {
  display: none;
}


.multiPageEditorHeading,
.multiPageEditorNotFound,
.multiPageEditorListConfirmed,
.multiPageEditorPagesEdited,
.multiPageEditorPagesUntargeted {
  line-height: 30px;
  text-align: center;
}

.multiPageEditorNotFound {
  transform: translateY(-76px);
}

[data-pages-found="false"] .multiPageEditorHeading {
  opacity: 0;
}

.multiPageEditorButton {
  display: block;
  padding: 6px;
  color: var(--text-color-main);
  background-color: rgb(161, 0, 0);
  background-image: radial-gradient(rgb(223, 0, 0), rgb(127, 0, 0));
  border: 2px solid rgb(131, 0, 0);
  border-radius: 4px;
  box-shadow: 0 0 12px rgba(255, 255, 255, 0.4);
  cursor: pointer;
}

.multiPageEditorButton:hover {
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.3);
  font-weight: 700;
}

.multiPageEditorButton.\--list {
  width: 100px;
  margin: 0 6px;
  padding: 3px 0;
  box-shadow: none;
}

.multiPageEditorButton.\--edit {
  display: inline-block;
  width: 174px;
  margin: 0 6px;
}

.multiPageEditorButton.\--edit.\--editNow {
  border: 1px dashed rgb(255, 255, 0);
  box-shadow: 0 0 12px rgba(255, 255, 0, 0.4);
}

.multiPageEditorButton.\--edit.\--editNow:hover {
  box-shadow: 0 0 12px rgba(255, 255, 0, 0.8);
}

.multiPageEditorButton.\--search,
.multiPageEditorButton.\--confirm {
  margin: 24px auto;
}

[data-form-action="input-text"] .multiPageEditorButton.\--search {
  transform: translateY(160px);
}

.caseControl {
  position: absolute;
  top: 14px;
  width: 36px;
  height: 36px;
  padding: 0;
  background-color: rgb(255, 0, 0);
  border: none;
  border-radius: 4px;
  box-shadow: 0 0 9px rgba(255, 255, 255, 0);
  opacity: 0.5;
  cursor: pointer;
  transition: all 0.3s linear;
}

.caseControl.\--caseSensitive {
  right: 59px;
  background-image: url(\'data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20lang%3D%22en-GB%22%20viewBox%3D%220%200%2020%2020%22%3E%3Ctitle%3ECase%20Sensitive%20Icon%3C%2Ftitle%3E%3Cdefs%3E%3Cstyle%3E%3C!%5BCDATA%5B%20path%20%7Bfill%3A%20rgb(255%2C255%2C255)%3Bfilter%3A%20url(%23drop-shadow)%3B%7D%20%23upper-case%2C%23lower-case%20%7Btransform%3A%20translate(-2px%2C-2px)%3B%7D%20%5D%5D%3E%3C%2Fstyle%3E%3Cfilter%20id%3D%22drop-shadow%22%3E%3CfeDropShadow%20dx%3D%220.25%22%20dy%3D%220.25%22%20stdDeviation%3D%220.25%22%20flood-color%3D%22rgb(0%2C0%2C0%2C0.3)%22%20%2F%3E%3CfeDropShadow%20dx%3D%220.25%22%20dy%3D%22-0.25%22%20stdDeviation%3D%220.25%22%20flood-color%3D%22rgb(0%2C0%2C0%2C0.3)%22%20%2F%3E%3CfeDropShadow%20dx%3D%22-0.25%22%20dy%3D%22-0.25%22%20stdDeviation%3D%220.25%22%20flood-color%3D%22rgb(0%2C0%2C0%2C0.3)%22%20%2F%3E%3CfeDropShadow%20dx%3D%22-0.25%22%20dy%3D%220.25%22%20stdDeviation%3D%220.25%22%20flood-color%3D%22rgb(0%2C0%2C0%2C0.3)%22%20%2F%3E%3C%2Ffilter%3E%3C%2Fdefs%3E%3Cg%20id%3D%22caseSensitive%22%3E%3Cpath%20id%3D%22upper-case%22%20d%3D%22M7.53%207L4%2017h2.063l.72-2.406h3.624l.72%202.406h2.062L9.65%207h-2.12zm1.064%201.53L9.938%2013H7.25l1.344-4.47z%22%20%2F%3E%3Cpath%20id%3D%22lower-case%22%20d%3D%22M18.55%2017l-.184-1.035h-.055c-.35.44-.71.747-1.08.92-.37.167-.85.25-1.44.25-.564%200-.955-.208-1.377-.625-.42-.418-.627-1.012-.627-1.784%200-.808.283-1.403.846-1.784.568-.386%201.193-.607%202.208-.64l1.322-.04v-.335c0-.772-.396-1.158-1.187-1.158-.61%200-1.325.18-2.147.55l-.688-1.4c.877-.46%201.85-.69%202.916-.69%201.024%200%201.59.22%202.134.662.545.445.818%201.12.818%202.03V17h-1.45m-.394-3.527l-.802.027c-.604.018-1.054.127-1.35.327-.294.2-.442.504-.442.912%200%20.58.336.87%201.008.87.48%200%20.865-.137%201.152-.414.29-.277.436-.645.436-1.103v-.627%22%20%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E\'), radial-gradient(rgb(255, 0, 0), rgb(127, 0, 0));
}

.caseControl.\--caseInsensitive {
  right: 16px;
  background-image: url(\'data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20lang%3D%22en-GB%22%20viewBox%3D%220%200%2014%2014%22%3E%3Ctitle%3ECase%20Insensitive%20Icon%3C%2Ftitle%3E%3Cdefs%3E%3Cstyle%3E%3C!%5BCDATA%5B%20path%20%7Bfill%3A%20rgb(255%2C255%2C255)%3Bfilter%3A%20url(%23drop-shadow)%3B%7D%20%23lower-case%20%7Btransform%3A%20translate(-10px%2C-6px)%3B%7D%20%5D%5D%3E%3C%2Fstyle%3E%3Cfilter%20id%3D%22drop-shadow%22%3E%3CfeDropShadow%20dx%3D%220.2%22%20dy%3D%220.2%22%20stdDeviation%3D%220.2%22%20flood-color%3D%22rgb(0%2C0%2C0%2C0.3)%22%20%2F%3E%3CfeDropShadow%20dx%3D%220.2%22%20dy%3D%22-0.2%22%20stdDeviation%3D%220.2%22%20flood-color%3D%22rgb(0%2C0%2C0%2C0.3)%22%20%2F%3E%3CfeDropShadow%20dx%3D%22-0.2%22%20dy%3D%22-0.2%22%20stdDeviation%3D%220.2%22%20flood-color%3D%22rgb(0%2C0%2C0%2C0.3)%22%20%2F%3E%3CfeDropShadow%20dx%3D%22-0.2%22%20dy%3D%220.2%22%20stdDeviation%3D%220.2%22%20flood-color%3D%22rgb(0%2C0%2C0%2C0.3)%22%20%2F%3E%3C%2Ffilter%3E%3C%2Fdefs%3E%3Cg%20id%3D%22case-insensitive%22%3E%3Cpath%20id%3D%22lower-case%22%20d%3D%22M18.55%2017l-.184-1.035h-.055c-.35.44-.71.747-1.08.92-.37.167-.85.25-1.44.25-.564%200-.955-.208-1.377-.625-.42-.418-.627-1.012-.627-1.784%200-.808.283-1.403.846-1.784.568-.386%201.193-.607%202.208-.64l1.322-.04v-.335c0-.772-.396-1.158-1.187-1.158-.61%200-1.325.18-2.147.55l-.688-1.4c.877-.46%201.85-.69%202.916-.69%201.024%200%201.59.22%202.134.662.545.445.818%201.12.818%202.03V17h-1.45m-.394-3.527l-.802.027c-.604.018-1.054.127-1.35.327-.294.2-.442.504-.442.912%200%20.58.336.87%201.008.87.48%200%20.865-.137%201.152-.414.29-.277.436-.645.436-1.103v-.627%22%20%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E\'), radial-gradient(rgb(255, 0, 0), rgb(127, 0, 0));
}

[data-case-sensitive="true"] .caseControl.\--caseSensitive,
[data-case-sensitive="false"] .caseControl.\--caseInsensitive {
  opacity: 1;
  box-shadow: 0 0 9px rgba(255, 255, 255, 0.65);
  outline: none;
}

.multiPageEditorSlot {
  margin: 0 6px;
  padding: 6px 12px;
  font-family: courier;
  font-weight: 400;
  color: var(--text-color-main);
  text-shadow: 1px 1px rgba(0, 0, 0, 0.5);
  background-color: rgb(0, 119, 200);
  background-image: radial-gradient(rgb(0, 151, 232), rgb(0, 23, 105));
  border-radius: 36px;
}

[data-case-sensitive="false"] .multiPageEditorSlot.\--find::after {
  content: \'/i\';
  padding-left: 4px;
  font-weight: 900;
  font-size: 15px;
  text-shadow: 2px 2px rgba(0, 0, 0, 0.5);
}

.pageList {
  margin-left: 12px;
  padding-left: 12px;
  font-family: courier;
  font-size: 12px;
}

.pageListItem {
  padding: 6px 0;
}

.pageListLink {
  display: inline-block;
  padding: 6px;
  color: rgba(255, 255, 127, 1);
  text-decoration: underline 1px solid rgba(255, 255, 127, 0);
  background-color: rgba(0, 0, 0, 0);
  border-radius: 4px;
  transition: all 0.3s linear;
}

[data-replace-activated="true"] .pageListLink {
  color: rgba(255, 255, 127, 0.5);
}

[data-replace-activated="true"] .pageListLink.\--blink {
  animation: blink 3.5s ease-in-out;
}

[data-replace-activated="true"] .pageListCheckbox:checked + .pageListLink {
  color: rgba(255, 255, 127, 1);
}

.pageListLink:hover {
  color: rgba(255, 255, 0, 1);
  background-color: rgba(0, 0, 0, 0.3);
  text-decoration: underline 1px solid rgba(255, 255, 0, 1);
}

[data-replace-activated="true"] .pageListLink:hover {
  color: rgba(255, 255, 127, 0.5);
  text-decoration: underline 1px solid rgba(255, 255, 127, 0.5);
}

[data-replace-activated="true"] .pageListCheckbox:checked + .pageListLink:hover {
  color: rgba(255, 255, 0, 1);
  text-decoration: underline 1px solid rgba(255, 255, 0, 1);
}

.pageListLink.\--edited {
  color: rgba(255, 255, 127, 1);
  background-color: rgba(0, 0, 0, 0.1);
  text-decoration: underline 1px solid rgba(255, 255, 127, 0);
}

.pageListLink.\--edited:hover {
  color: rgba(255, 255, 127, 1);
  background-color: rgba(0, 0, 0, 0.3);
  text-decoration: underline 1px solid rgba(255, 255, 127, 1);
}


@keyframes blink {

  12.5% {color: rgba(255, 255, 0, 1); background-color: rgb(131, 0, 0);}
  25% {color: rgba(255, 255, 127, 0.5); background-color: rgba(0, 0, 0, 0);}
  37.5% {color: rgba(255, 255, 0, 1); background-color: rgb(131, 0, 0);}
  50% {color: rgba(255, 255, 127, 0.5); background-color: rgba(0, 0, 0, 0);}
  62.5% {color: rgba(255, 255, 0, 1); background-color: rgb(131, 0, 0);}
  75% {color: rgba(255, 255, 127, 0.5); background-color: rgba(0, 0, 0, 0);}
  87.5% {color: rgba(255, 255, 0, 1); background-color: rgb(131, 0, 0);}
}


@media only screen and (max-width: 842px) {
  
  .multiPageEditorForm {
    width: calc(100vw - 24px - 17px);
    margin: 0 12px;
  }
}


@media only screen and (max-aspect-ratio: 1/1) {
  
  h1 {
    font-size: 7vw;
  }
}


@media only screen and (pointer: coarse) and (hover: none) {

  .multiPageEditorForm {
    width: calc(100vw - 12px);
    margin: 0 6px;
  }

  .multiPageEditorFieldset {
    padding: 4px;
  }

  .multiPageEditorLegend {
    color: rgba(255, 255, 255, 0.7);
  }

  .multiPageEditorSlot {
    margin: 0 3px;
  }

  .caseControl.\--caseSensitive {
    right: 51px;
  }

  .caseControl.\--caseInsensitive {
    right: 8px;
  }
}

</style>
<link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20viewBox=\'0%200%201200%201200\'%3E%3Cdefs%3E%3CclipPath%20id=\'square-edge\'%3E%3Crect%20x=\'0\'%20y=\'0\'%20width=\'1200\'%20height=\'1200\'%20/%3E%3C/clipPath%3E%3CclipPath%20id=\'circle-edge\'%3E%3Ccircle%20cx=\'600\'%20cy=\'600\'%20r=\'640\'%20/%3E%3C/clipPath%3E%3Cstyle%3E%3C!%5BCDATA%5B%20.square%20%7B%20clip-path:%20url(%23square-edge);%0A%7D%20.upperLeft%20%7B%20width:%201200px;%20height:%201200px;%20fill:%20rgb(154,%200,%200);%0A%7D%20.lowerRight%20%7B%20width%20:%201700px;%20height:%201700px;%20fill:%20rgba(0,%200,%200,%200.5);%0A%7D%20text%20%7B%20font-family:%20\'Arial%20Black\';%20font-size:%20700px;%20fill:%20rgba(255,%20255,%20255,%200.4);%0A%7D%20circle%20%7B%20stroke:%20rgba(255,%20255,%20255,%200.4);%20stroke-width:%20144;%20fill:%20none;%0A%7D%20%5D%5D%3E%3C/style%3E%3C/defs%3E%3Cg%20class=\'square\'%3E%3Crect%20class=\'upperLeft\'%20x=\'0\'%20y=\'0\'%20/%3E%3Crect%20class=\'lowerRight\'%20x=\'0\'%20y=\'0\'%20transform=\'rotate(45,%200,%200),%20translate(849,%20-849)\'%20/%3E%3C/g%3E%3Ctext%20x=\'328\'%20y=\'840\'%3EA%3C/text%3E%3Ccircle%20cx=\'600\'%20cy=\'600\'%20r=\'510\'%20/%3E%3C/svg%3E" type="image/svg+xml" />

</head>

<body>
<header>
<h1>Ashiva MultiPage Editor</h1>
</header>

<form class="multiPageEditorForm"
data-form-action="'.$Form_Action.'"
data-find-phrase="'.$Find_Phrase.'"
data-case-sensitive="'.$Case_Sensitive.'"
data-replace-activated="'.$Replace_Activated.'"
data-replace-phrase="'.$Replace_Phrase.'"
data-pages-found="'.$Pages_Found.'"
data-pages-edited="'.$Pages_Edited.'"
data-pages-untargeted="'.$Pages_Untargeted.'">

<fieldset class="multiPageEditorFieldset">
<legend class="multiPageEditorLegend"><span class="multiPageEditorLegendSpan --find">Find Text on Multiple Pages:</span><span class="multiPageEditorLegendSpan --replace">Replace Text across the Site:</span></legend>
<label class="multiPageEditorLabel --find"><input type="text" class="multiPageEditorInput --find" name="find" placeholder="Find this text..." value="'.str_replace('␀', '', $Find_Phrase).'" /></label>
<label class="multiPageEditorLabel --replace"><input type="text" class="multiPageEditorInput --replace" name="replace" placeholder="Replace with this text..." value="'.str_replace('␀', '', $Replace_Phrase).'" /></label>
<button type="button" class="multiPageEditorButton --search">Find <span class="multiPageEditorButtonSpan">and Edit</span> Multiple Pages</button>
<button type="button" class="caseControl --caseSensitive"></button>
<button type="button" class="caseControl --caseInsensitive"></button>
</fieldset>

<section class="pagesSearched">
<h2 class="multiPageEditorHeading --find">You can find <span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> on any of these pages<span class="listening">...</span><span class="searching">:</span></h2>
<h2 class="multiPageEditorHeading --remove">You can remove <span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> from any of these pages<span class="listening">...</span><span class="searching">:</span></h2>
<h2 class="multiPageEditorHeading --replace">You can replace <span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> with <span class="multiPageEditorSlot --replace">'.str_replace('␀', '🚫', $Replace_Phrase).'</span> on any of these pages<span class="listening">...</span><span class="searching">:</span></h2>
<h2 class="multiPageEditorNotFound">You cannot find <span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> on any page.</h2>
<h2 class="multiPageEditorListConfirmed --remove">Remove <span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> from these pages:</h2>
<h2 class="multiPageEditorListConfirmed --replace">Replace <span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> with <span class="multiPageEditorSlot --replace">'.str_replace('␀', '🚫', $Replace_Phrase).'</span> on these pages:</h2>

<div class="multiPageEditorButtonGroup --list">
<button type="button" class="multiPageEditorButton --list --checkAll">Check all</button>
<button type="button" class="multiPageEditorButton --list --uncheckAll">Uncheck all</button>
</div>

<ol class="pageList --toEdit">
';

for ($i = 0; $i < count($Refined_Page_List_Array); $i++) {

  $Page_URL = str_replace('/home/domains/vol2/961/2034961/user/htdocs/.assets/content/pages', '', $Refined_Page_List_Array[$i]);
  $Page_URL .= '/index.php';

  echo '<li class="pageListItem --toEdit">
  <input type="checkbox" class="pageListCheckbox" />
  <a class="pageListLink --toEdit" href="'.$Page_URL.'" target="_blank">'.$Page_URL.'</a>
  </li>'."\n";
}

echo '
</ol>

<button type="button" class="multiPageEditorButton --confirm">Confirm Pages to Edit</button>
</section>


<section class="pagesEdited">
<h2 class="multiPageEditorPagesEdited --removed"><span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> removed from these pages:</h2>
<h2 class="multiPageEditorPagesEdited --replaced"><span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> replaced with <span class="multiPageEditorSlot --replace">'.str_replace('␀', '🚫', $Replace_Phrase).'</span> on these pages:</h2>

<ol class="pageList --edited">
';

for ($i = 0; $i < count($Pages_Edited_Array); $i++) {

  echo '<li class="pageListItem --edited">
  <a class="pageListLink --edited" href="'.$Pages_Edited_Array[$i].'" target="_blank">'.$Pages_Edited_Array[$i].'</a>
  </li>'."\n";
}

echo '
</ol>
</section>


<section class="pagesUntargeted">
<h2 class="multiPageEditorPagesUntargeted --removed"><span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> not removed on these pages:</h2>
<h2 class="multiPageEditorPagesUntargeted --replaced"><span class="multiPageEditorSlot --find">'.str_replace('␀', '🚫', $Find_Phrase).'</span> not replaced on these pages:</h2>

<ol class="pageList --untargeted">
';

for ($i = 0; $i < count($Pages_Untargeted_Array); $i++) {

  echo '<li class="pageListItem --untargeted">
  <a class="pageListLink --untargeted" href="'.$Pages_Untargeted_Array[$i].'" target="_blank">'.$Pages_Untargeted_Array[$i].'</a>
  </li>'."\n";
}

echo '
</ol>
</section>

<div class="multiPageEditorButtonGroup --edit">
<button type="button" class="multiPageEditorButton --edit --back">Go Back to Page Search</button>
<button type="button" class="multiPageEditorButton --edit --editNow">Edit Pages Now</button>
<button type="button" class="multiPageEditorButton --edit --reinitialise">Re-initialise Editor</button>
</div>

</form>

<script>

  //***************//
 // SET VARIABLES //
//***************//

const appState = {};
appState.formAction = \''.$Form_Action.'\';
appState.findPhrase = \''.$Find_Phrase.'\';
appState.caseSensitive = \''.$Case_Sensitive.'\';
appState.replacePhrase = \''.$Replace_Phrase.'\';
appState.replaceActivated = \''.$Replace_Activated.'\';
appState.pagesFound = \''.$Pages_Found.'\';

const multiPageEditorForm = document.getElementsByClassName(\'multiPageEditorForm\')[0];
const multiPageEditorButton = document.getElementsByClassName(\'multiPageEditorButton\')[0];
const checkAllButton = document.querySelector(\'.multiPageEditorButton.\--list.\--checkAll\');
const uncheckAllButton = document.querySelector(\'.multiPageEditorButton.\--list.\--uncheckAll\');
const findInput = document.querySelector(\'.multiPageEditorInput.\--find\');
const replaceLabel = document.querySelector(\'.multiPageEditorLabel.\--replace\');
const replaceInput = document.querySelector(\'.multiPageEditorInput.\--replace\');
const findSlots = [...document.querySelectorAll(\'.multiPageEditorSlot.\--find\')];
const replaceSlots = [...document.querySelectorAll(\'.multiPageEditorSlot.\--replace\')];
const caseSensitiveButton = document.querySelector(\'.caseControl.\--caseSensitive\');
const caseInsensitiveButton = document.querySelector(\'.caseControl.\--caseInsensitive\');
const confirmButton = document.querySelector(\'.multiPageEditorButton.\--confirm\');
const backButton = document.querySelector(\'.multiPageEditorButton.\--edit.\--back\');
const editNowButton = document.querySelector(\'.multiPageEditorButton.\--edit.\--editNow\');
const reinitialiseButton = document.querySelector(\'.multiPageEditorButton.\--edit.\--reinitialise\');



  //***********//
 // FUNCTIONS //
//***********//


const updateQueryString = (appState) => {

  let queryStringArray = [];

  for (let appStateKey in appState) {

    queryStringArray.push(appStateKey + \'=\' + appState[appStateKey]);
  }

  let appURL = window.location.href.split(\'?\')[0];
  let queryString = \'?\' + queryStringArray.join(\'&\');

  window.history.pushState({}, document.title, appURL + queryString);

  return queryString;
}


const parseQueryParameters = (queryParameters) => {

  switch (queryParameters.get(\'replaceActivated\')) {

    case (\'true\') :

      replaceInput.disabled = false;
      if (document.activeElement !== findInput) { replaceInput.focus(); }
      break;


    case (\'false\') :

      replaceInput.disabled = true;
      break;
  }


  let findPhrase = queryParameters.get(\'findPhrase\');
  let replacePhrase = queryParameters.get(\'replacePhrase\');

  for (let findSlot of findSlots) {

    findSlot.textContent = findPhrase.replace(\'␀\', \'🚫\');
  }

  for (let replaceSlot of replaceSlots) {

    replaceSlot.textContent = replacePhrase.replace(\'␀\', \'🚫\');
  }
}


const updateApp = (appState) => {

  let queryString = updateQueryString(appState);

  let queryParameters = new URLSearchParams(queryString);

  for (let entry of queryParameters.entries()) {

    let keyArray = entry[0].split(/(?=[A-Z])/);
    keyArray[1] = keyArray[1].toLowerCase();
    
    let key = \'data-\' + keyArray.join(\'-\');
    let value = entry[1];

    multiPageEditorForm.setAttribute(key, value);
  }

  parseQueryParameters(queryParameters);
}


const activateReplace = (e) => {

  if (e.target.nodeName === \'LABEL\') {

    appState.replaceActivated = \'true\';
    updateApp(appState);
  }
}


const deactivateReplace = () => {

  appState.replaceActivated = \'false\';

  if (appState.findPhrase === \'␀\') {

    appState.formAction = \'initialise\';
  }

  updateApp(appState);
}


const checkFindInputValue = (e) => {

  if (findInput.value.length < 1) {

    appState.findPhrase = \'␀\';

    if (appState.replacePhrase === \'␀\') {
      
      appState.replaceActivated = \'false\';
      appState.formAction = \'initialise\';
    }
  }

  else {
    
    appState.formAction = \'input-text\';
  }

  appState.pagesFound = \'true\';
  appState.findPhrase = findInput.value || \'␀\';
  appState.replacePhrase = replaceInput.value || \'␀\';

  updateApp(appState);
}


const checkReplaceInputValue = () => {

  if (replaceInput.value.length < 1) {

    deactivateReplace();
  }
  
  appState.findPhrase = findInput.value || \'␀\';
  appState.replacePhrase = replaceInput.value || \'␀\';

  updateApp(appState);
}


const editMultiplePages = () => {

  let urlParams = new URLSearchParams(window.location.search);
  
  findInput.setAttribute(\'required\', \'required\');
  if (findInput.checkValidity() !== true) {return false;}

  let findValue = findInput.value;
  let replaceValue = (replaceInput.disabled === true) ? \'\' : replaceInput.value;
  let caseSensitivity = urlParams.get(\'caseSensitive\');

  newQuery = \'\';
  newQuery += window.location.href.split(\'?\')[0];
  newQuery += \'?formAction=search-pages\';
  newQuery += \'&findPhrase=\' + findValue;
  newQuery += \'&caseSensitive=\' + caseSensitivity;

  if (replaceValue !== \'\') {
  
    newQuery += \'&replacePhrase=\' + replaceValue;
    newQuery += \'&replaceActivated=\' + urlParams.get(\'replaceActivated\');
  }

  newQuery += \'&pagesFound=␀\';

  window.location.href = newQuery;  // <= THIS SHOULD PROBABLY BE PUSHSTATE, NO (?)
}


const confirmMultiplePages = () => {

  let listCheckboxes = [...document.querySelectorAll(\'.pageListCheckbox\')];

  let pagesSelected = false;

  for (let listCheckbox of listCheckboxes) {

    if (listCheckbox.checked === true) {

      pagesSelected = true;
      break;
    }
  }

  if (pagesSelected === false) {

    let listLinks = [...document.querySelectorAll(\'.pageListLink\')];

    for (let listLink of listLinks) {
      
      listLink.classList.add(\'--blink\');
    }

    setTimeout(() => {

      for (let listLink of listLinks) {
        
        listLink.classList.remove(\'--blink\');
      }
    }, 3500);
  }

  else {

    appState.formAction = \'confirm-pages\';
    updateApp(appState);
  }
}


const checkAllBoxes = () => {

  let listCheckboxes = [...document.querySelectorAll(\'.pageListCheckbox\')];

  for (let listCheckbox of listCheckboxes) {

    listCheckbox.checked = true;
  }
}


const uncheckAllBoxes = () => {

  let listCheckboxes = [...document.querySelectorAll(\'.pageListCheckbox\')];

  for (let listCheckbox of listCheckboxes) {

    listCheckbox.checked = false;
  }
}


const activateCaseButton = (e) => {

  appState.caseSensitive = (e.target === caseSensitiveButton) ? \'true\' : \'false\';
  appState.pagesFound = \'true\';
  appState.formAction = \'input-text\';
  updateApp(appState);
}


const returnToSearch = () => {
  
  appState.pagesEdited = \'␀\';
  appState.pagesUntargeted = \'␀\';
  appState.formAction = \'search-pages\';
  updateApp(appState);
}


const editPagesNow = () => {

  const pagesTargeted = [];
  const pagesUntargeted = [];
  const pageLinks = [... document.querySelectorAll(\'.pageListLink\')];
  const selectedPageLinks = [... document.querySelectorAll(\'.pageListCheckbox:checked + .pageListLink\')];

  for (let pageLink of pageLinks) {

    if (selectedPageLinks.indexOf(pageLink) > -1) {

      pagesTargeted.push(pageLink.textContent);
    }

    else {
    
      pagesUntargeted.push(pageLink.textContent);
    }
  }

  let editPagesNowURL = \'\';
  editPagesNowURL += window.location.href;
  editPagesNowURL += \'&pagesTargeted=\' + encodeURIComponent(JSON.stringify(pagesTargeted));
  editPagesNowURL += \'&pagesUntargeted=\' + encodeURIComponent(JSON.stringify(pagesUntargeted));
  editPagesNowURL += \'&editPagesNow=true\';

  window.location.href = editPagesNowURL;
}


const reinitialise = () => {

  window.location.href = window.location.href.split(\'?\')[0];
}


  //*****************//
 // EVENT LISTENERS //
//*****************//

replaceLabel.addEventListener(\'click\', activateReplace, false);
findInput.addEventListener(\'keyup\', checkFindInputValue, false);
replaceInput.addEventListener(\'keyup\', checkReplaceInputValue, false);
multiPageEditorButton.addEventListener(\'click\', editMultiplePages, false);
confirmButton.addEventListener(\'click\', confirmMultiplePages, false);
checkAllButton.addEventListener(\'click\', checkAllBoxes, false);
uncheckAllButton.addEventListener(\'click\', uncheckAllBoxes, false);
caseSensitiveButton.addEventListener(\'mouseover\', activateCaseButton, false);
caseInsensitiveButton.addEventListener(\'mouseover\', activateCaseButton, false);
backButton.addEventListener(\'click\', returnToSearch, false);
editNowButton.addEventListener(\'click\', editPagesNow, false);
reinitialiseButton.addEventListener(\'click\', reinitialise, false);

</script>

</body>
</html>';

?>
