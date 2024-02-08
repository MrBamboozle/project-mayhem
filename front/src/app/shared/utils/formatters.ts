import { Address } from "../models/location";

export function formatTopSecretFontTitle(title: string): string {
  return title.split(' ').map(word => `{${word}}`).join('');
}

export function formatAddress(address: Address): string {
  let formattedAddress: string = '';

  if(address?.road) {
    formattedAddress += address.road;

    if(address?.houseNumber) {
      formattedAddress += ' ' + address.houseNumber;
    }

    formattedAddress += ', ';
  }

  if(address?.city) {
    formattedAddress += address.city;
  } else if (address?.state) {
    formattedAddress += address.state;
  } else if (address?.country) {
    formattedAddress += address.country;
  }

  return formattedAddress ? formattedAddress : 'Middle of nowhere';
}

export function formatDateToLocale(date: string): string {
  const dateObj = new Date(date);
  return dateObj.toLocaleString();
}

export function formatDateToDateTimeLocal(datetimeString: string): string {
  // Assuming your input format is "YYYY-MM-DD HH:mm:ss"
  const [date, time] = datetimeString.split(' ');
  const [hours, minutes] = time.split(':');

  // Return the formatted string without seconds, and with 'T' separator
  return `${date}T${hours}:${minutes}`;
}