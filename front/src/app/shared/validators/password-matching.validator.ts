import { AbstractControl, FormGroup, ValidationErrors, ValidatorFn } from '@angular/forms';

export function passwordMatchingValidator(): ValidatorFn {
  return (control: AbstractControl): ValidationErrors | null => {
    if (!(control instanceof FormGroup)) {
      throw new Error('passwordMatchingValidator must be applied to a FormGroup');
    }

    const password = control.get('password')?.value;
    const repeatPassword = control.get('repeatPassword')?.value;

    if (password && repeatPassword && password !== repeatPassword) {
      // Return an error object if passwords don't match
      return { passwordsNotMatching: true };
    }

    // Return null if validation passes
    return null;
  };
}
