export interface User {
  id: string;
  createdAt: string;
  updatedAt: string;
  name: string;
  email: string;
  emailVerifiedAt: string;
}

export interface UserRequest {
  name: string;
  email: string;
}