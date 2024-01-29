import { User } from "./user";
export interface Event {
  id: string;
  createdBy: User;
  createdAt: string;
  updatedAt: string;
  title: string;
  tagLine: string;
  description: string;
  time: string;
  location: string;
  address: any;
}

export interface CreateEventRequest {
  title: string;
  tagLine?: string;
  description: string;
  startTime: string;
  endTime: string;
  location: string;
  address?: any;
  categories?: string[];
}