<h3>Guest Search</h3>
<p>To register, or to check your registration, just type in your name into the box below and click <span class="reference">Search</span>.
	You can use your first name, last name, nickname or any combination*. If there is more than one match, you'll be
	asked to choose from a list.</p>
<?php
  $class_name = $Page->final_class_name ('GUEST_SEARCH_FORM', 'hochzeit/forms/guest_search_form.php');
  $form = new $class_name ($Page);
  $form->process_plain ();
  $form->display ();
?>
<p class="notes">*Words or names with three letters or less will not get searched. Sorry, it's a limitation
	of the database I'm using.</p>