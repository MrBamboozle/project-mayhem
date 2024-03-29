import { User } from "./user";

export interface LoginResponse {
  user: User;
  token: string;
  refreshToken: string;
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RefreshTokenResponse {
  token: string;
  refreshToken: string;
}

export interface RegisterRequest {
  email: string;
  name: string;
  password: string;
  repeatPassword: string;
}

export interface ForgotPasswordRequest {
  email: string;
}