import { config } from "@app/core/app-config";
import { User } from "../models/user";

export function userAvatarSrc(user: User): string {
  return `${config.BACKEND_URL}${user.avatar.path}`;
}