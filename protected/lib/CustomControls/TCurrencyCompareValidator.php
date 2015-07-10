<?php
Prado::using ('System.Web.UI.WebControls.TCompareValidator');

class TCurrencyCompareValidator extends TCompareValidator {
    protected function getComparisonValues ($value1,$value2) {
        if ($this->getDataType()===CurrencyDataType::Currency) {
            $value1=str_replace('.','',$value1);
            $value2=str_replace('.','',$value2);
            return array($value1,$value2);
        }
        return parent::getComparisonValue($value1,$value2);
    }

    public function setDataType ($value) {
        $this->setViewState('DataType', TPropertyValue::ensureEnum($value, 'CurrencyDataType'), CurrencyDataType::String);
    }
    public function getDataType () {
        return $this->getViewState('DataType', CurrencyDataType::String);
    }
}

class CurrencyDataType extends TValidationDataType {
    const Currency='IDR';
}
?>
