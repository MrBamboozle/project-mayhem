export interface Notification {
  id: string;
  userId: string;
  title: string;
  description: string;
  read: 0 | 1;
  eventId: string;
}