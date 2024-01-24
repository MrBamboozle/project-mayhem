import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';

export function dateTimeValidator(): ValidatorFn {
  return (control: AbstractControl): ValidationErrors | null => {
    if (!control.value) {
      // if control is empty, return no error
      return null;
    }

    // Check if the control value matches the datetime-local format
    const dateTimePattern = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
    const isValidFormat = dateTimePattern.test(control.value);

    if (!isValidFormat) {
      // If it doesn't match the pattern, it's not a valid datetime-local format
      return { 'invalidDateTime': { value: control.value } };
    }

    // Attempt to convert the control value to a date
    const date = new Date(control.value);

    // Check if the date is valid
    const isValidDate = !isNaN(date.getTime());

    // Return null if the date is valid and matches the datetime-local format,
    // or return the corresponding error
    return isValidDate ? null : { 'invalidDateTime': { value: control.value } };
  };
}