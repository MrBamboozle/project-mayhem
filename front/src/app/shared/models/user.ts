import { RoleEnum } from "../enums/roles";

export interface User {
  id: string;
  createdAt: string;
  updatedAt: string;
  name: string;
  email: string;
  emailVerifiedAt: string;
  avatar: Avatar;
  role: Role;
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

export interface Role {
  id: string;
  name: RoleEnum;
}