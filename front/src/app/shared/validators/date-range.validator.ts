import { AbstractControl, FormGroup, ValidationErrors, ValidatorFn } from '@angular/forms';

export function dateRangeValidator(): ValidatorFn {

  return (control: AbstractControl): ValidationErrors | null => {
    if (!(control instanceof FormGroup)) {
      throw new Error('passwordMatchingValidator must be applied to a FormGroup');
    }

    const dateFrom = control.get('dateFrom')?.value;
    const dateTo = control.get('dateTo')?.value;

    // Check if both dates are valid
    if (dateFrom && dateTo && new Date(dateFrom) >= new Date(dateTo)) {
      // If dateFrom is after dateTo, return an error
      return { 'dateRangeInvalid': true };
    }

    // Return null if validation passes
    return null;
  };
};