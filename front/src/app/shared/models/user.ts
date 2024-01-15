export interface User {
  id: string;
  createdAt: string;
  updatedAt: string;
  name: string;
  email: string;
  emailVerifiedAt: string;
  avatar: Avatar;
}

export interface UserRequest {
  name: string;
  email: string;
}

export interface UserEditRequest {
  name?: string;
  password?: string;
  repeatPassword?: string;
}

export interface Avatar {
  id: string;
  path: string;
}