<?php

namespace App\Models;

class DoseUnits extends LabelledEnum
{

  const Application = ['id' => 0, 'label' => 'application'];
  const Cap         = ['id' => 1, 'label' => 'cap'];
  const Mg          = ['id' => 2, 'label' => 'mg'];
  const Min         = ['id' => 3, 'label' => 'min'];
  const ML          = ['id' => 4, 'label' => 'mL'];
  const Puff        = ['id' => 5, 'label' => 'puff'];
  const Softgel     = ['id' => 6, 'label' => 'softgel'];
  const Spray       = ['id' => 7, 'label' => 'spray'];
  const Tab         = ['id' => 8, 'label' => 'tab'];
  const Unit        = ['id' => 9, 'label' => 'unit'];
  const Vial        = ['id' => 10, 'label' => 'vial'];
}
