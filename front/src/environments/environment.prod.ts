declare let require: any;

const DEFAULTS = {
  name: 'production',
  production: true,
  apiUrl: 'https://199.247.16.83/api',
  backendUrl: 'https://199.247.16.83',
  version: '1.0.0',
};

export const environment = { ...DEFAULTS, ...(window as any).APP_CONFIG };
