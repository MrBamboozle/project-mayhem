import { User } from "./user";

export interface Event {
  id: string;
  createdBy: User;
  createdAt: string;
  updatedAt: string;
  title: string;
  tagline: string;
  description: string;
  time: string;
  location: string;
}

export interface EventRequest {
  title: string;
  tagline: string;
  description: string;
  time: string;
  location: string;
}