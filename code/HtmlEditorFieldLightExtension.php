<?php

/**
 * Checks for whether Lightbox is enabled and adds the Lightbox option/fields to type.
 *
 * Removes Anchor link types for Lightboxes, since having a lightbox within lightbox is not within our scope yet.
 */
class HtmlEditorField_ToolbarLightboxExtension extends DataExtension {

	public function updateLinkForm(&$form) {
		$enabled = (int)$this->owner->request->getVar('lightbox');
		$fields = $form->fields;
		/* @var $fields FieldList */

		$field = $fields->dataFieldByName('LinkType');
		$options = $field->getSource();

		if ($enabled) {
			$options['lightbox'] = 'Lightbox';
		} else {
			// remove "anchor" for lightbox, since it's page specific
			// TODO: perhaps move this to a separate flag?
			unset($options['anchor']);
		}
		$field->setSource($options);
		$fields->replaceField('LinkType', $field);

		// add the list of lightboxes available
		if ($enabled) {
			$fields->insertAfter(LightboxAdmin::getLightboxField('lightbox', 'Lightbox'),
				'internal');

			$form->setFields($fields);
		}

		// also add an option to have the lightbox open by default
		if ($enabled) {
			$fields->insertAfter(CheckboxField::create('LightboxOpenByDefault', 'Lightbox open by default'), 'lightbox');

		}
	}
}

/**
 * Adds the lightbox_admin javascript to the Admin interface when a HtmlEditorField is present.
 *
 * This helps handle enabling lightbox for the LinkForm, and pre-populating the lightbox already selected for an existing link.
 */
class HtmlEditorFieldLightboxExtension extends DataExtension {
	public function onBeforeRender () {
		if ($this->owner instanceof HtmlEditorField) {
			Requirements::javascript('lightbox/javascript/lightbox_admin.js');
		}
	}
}