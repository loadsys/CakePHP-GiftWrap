<?php

// Adds the Presenter object type so serializer objects
// can be found in conventional places
App::build(array(
  'Presenter' => array('%s' . 'Presenter' . DS)
), App::REGISTER);
