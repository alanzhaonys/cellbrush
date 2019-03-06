<?php

namespace AlanZhao\Cellbrush\Html;

interface ElementInterface extends AttributesBuilderInterface {

  /**
   * @return string
   */
  function render();
}
