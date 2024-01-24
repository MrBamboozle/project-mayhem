import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';

export function dateValidator(): ValidatorFn {
  return (control: AbstractControl): ValidationErrors | null => {
    if (!control.value) {
      // if control is empty return no error
      return null;
    }

    // Attempt to convert the control value to a date
    const date = new Date(control.value);

    // Check if the date is valid
    const isValidDate = !isNaN(date.getTime());

    // Return null if the date is valid, or an error object if not
    return isValidDate ? null : { 'invalidDate': { value: control.value } };
  };
}