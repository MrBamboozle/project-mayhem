declare let require: any;

const DEFAULTS = {
  name: 'production',
  production: true,
  apiUrl: '',
  version: '1.0.0',
};

export const environment = { ...DEFAULTS, ...(window as any).APP_CONFIG };
