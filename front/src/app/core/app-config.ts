import { environment } from '@env/environment';

interface appConfig {
  API_URL: string;
  BACKEND_URL: string;
  DEFAULT_PAGE: string;
  MIN_PASSWORD_LENGTH: number;
}

export const config: appConfig = {
  API_URL: environment.apiUrl,
  BACKEND_URL: environment.backendUrl,
  DEFAULT_PAGE: '/dashboard',
  MIN_PASSWORD_LENGTH: 5,
};
