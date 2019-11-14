<?php

namespace MdsSupportingClasses;

use MdsExceptions\InvalidResourceDataException;

class MdsCheckoutFields
{
    /**
     * @var array
     */
    protected $defaultFields = [];

    /**
     * CheckoutFields constructor.
     *
     * @param array $defaultFields
     */
    public function __construct(array $defaultFields)
    {
        $this->defaultFields = $defaultFields;
    }

    /**
     * @param string|null $prefix
     *
     * @return array
     */
    public function getCheckoutFields($prefix = null)
    {
        $service = MdsColliveryService::getInstance();

        if (!$service->isEnabled()) {
            if (isset($this->defaultFields[$prefix])) {
                return $this->defaultFields[$prefix];
            } else {
                return $this->defaultFields;
            }
        }

        try {
            $resources = MdsFields::getResources($service);

            if ($prefix) {
                $prefix = $prefix.'_';
            }
            $towns = ['' => 'Select Town'] + array_combine($resources['towns'], $resources['towns']);
            $location_types = ['' => 'Select Premises Type'] + array_combine($resources['location_types'], $resources['location_types']);
	        $customer = WC()->customer;
	        $cityPrefix = $prefix ? $prefix : 'billing_';
	        $townName = $customer->{"get_{$cityPrefix}city"}();
	        $suburbs = ['' => 'First select town/city'];

	        if ($townName) {
		        $townId = array_search($townName, $resources['towns']);
		        $suburbs = $suburbs + $service->returnColliveryClass()->getSuburbs($townId);
	        }

            return [
                $prefix.'country' => [
                    'priority' => 1,
                    'type' => 'country',
                    'label' => 'Country',
                    'required' => true,
                    'autocomplete' => 'country',
                    'class' => ['form-row-wide', 'address-field', 'update_totals_on_change'],
                ],
                $prefix.'state' => [
                    'priority' => 2,
                    'type' => 'state',
                    'label' => 'Province',
                    'required' => true,
                    'class' => ['form-row-wide', 'address-field', 'update_totals_on_change'],
                    'placeholder' => 'Please select',
                    'validate' => ['state'],
                    'autocomplete' => 'address-level1',
                ],
                $prefix.'city' => [
                    'priority' => 3,
                    'type' => 'select',
                    'label' => 'Town / City',
                    'required' => true,
                    'placeholder' => 'Please select',
                    'options' => $towns,
                    'class' => ['form-row-wide', 'address-field', 'update_totals_on_change'],
                ],
                $prefix.'suburb' => [
                    'priority' => 4,
                    'type' => 'select',
                    'label' => 'Suburb',
                    'required' => true,
                    'placeholder' => 'Please select',
                    'class' => ['form-row-wide', 'address-field'],
                    'options' => $suburbs,
                ],
                $prefix.'location_type' => [
                    'priority' => 5,
                    'type' => 'select',
                    'label' => 'Location Type',
                    'required' => true,
                    'class' => ['form-row-wide', 'address-field', 'update_totals_on_change'],
                    'placeholder' => 'Please select',
                    'options' => $location_types,
                    'default' => 'Private House',
                    'selected' => '',
                ],
                $prefix.'company' => [
                    'priority' => 6,
                    'label' => 'Company Name',
                    'placeholder' => 'Company (optional)',
                    'autocomplete' => 'organization',
                    'class' => ['form-row-wide'],
                ],
                $prefix.'address_1' => [
                    'priority' => 7,
                    'label' => 'Street',
                    'placeholder' => 'Street number and name.',
                    'autocomplete' => 'address-line1',
                    'required' => true,
                    'class' => ['form-row-wide'],
                ],
                $prefix.'address_2' => [
                    'priority' => 8,
                    'label' => 'Building Details',
                    'placeholder' => 'Apartment, suite, unit etc. (optional)',
                    'class' => ['form-row-wide'],
                    'autocomplete' => 'address-line2',
                    'required' => false,
                ],
                $prefix.'postcode' => [
                    'priority' => 9,
                    'label' => 'Postal Code',
                    'placeholder' => 'Postal Code',
                    'required' => false,
                    'class' => ['form-row-wide'],
                ],
                $prefix.'first_name' => [
                    'priority' => 10,
                    'label' => 'First Name',
                    'placeholder' => 'First Name',
                    'autocomplete' => 'given-name',
                    'required' => true,
                    'class' => ['form-row-wide'],
                ],
                $prefix.'last_name' => [
                    'priority' => 11,
                    'label' => 'Last Name',
                    'placeholder' => 'Last Name',
                    'autocomplete' => 'family-name',
                    'required' => true,
                    'class' => ['form-row-wide'],
                ],
                $prefix.'phone' => [
                    'priority' => 12,
                    'validate' => ['phone'],
                    'label' => 'Cell Phone',
                    'placeholder' => 'Phone number',
                    'required' => true,
                    'class' => ['form-row-wide'],
                ],
                $prefix.'email' => [
                    'priority' => 13,
                    'validate' => ['email'],
                    'label' => 'Email Address',
                    'placeholder' => 'you@yourdomain.co.za',
                    'required' => true,
                    'class' => ['form-row-wide'],
                ],
                $prefix.'postcode' => [
                    'priority' => 14,
                    'label' => 'Postal Code',
                    'placeholder' => 'Postal Code',
                    'required' => false,
                    'class' => ['form-row-wide', 'address-field', 'update_totals_on_change'],
                ],
            ];
        } catch (InvalidResourceDataException $e) {
            return $prefix ? $this->defaultFields[$prefix] : $this->defaultFields;
        }
    }
}
