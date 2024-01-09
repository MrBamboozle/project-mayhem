import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';

export function usernameValidator(): ValidatorFn {
  return (control: AbstractControl): ValidationErrors | null => {
    const forbiddenCharacters = /[&=_'\-+,<>]/;
    const periodCount = (control.value.match(/\./g) || []).length;

    if (forbiddenCharacters.test(control.value) || periodCount > 1) {
      // Return an error object if validation fails
      return { invalidUsername: true };
    }

    // Return null if validation passes
    return null;
  };
}
