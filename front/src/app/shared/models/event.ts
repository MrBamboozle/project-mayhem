import { User } from "./user";
export interface Event {
  id: string;
  creator: User;
  createdAt: string;
  updatedAt: string;
  title: string;
  tagLine: string;
  description: string;
  location: string;
  address: any;
  startTime: string;
  endTime: string;
  categories: Category[];
  engagingUsersTypes: EngagingUserType[];
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

export interface Category {
  id: string;
  name: string;
}

export interface EngagingUserType {
  createdAt: string;
  engagementType: EngagementType;
  id: string;
  updatedAt: string;
  user: User;
}

export enum EngagementType {
  watch = 'watch',
  attend = 'attend',
  detach = 'detach',
}

export interface EngagementTypeRequest {
}