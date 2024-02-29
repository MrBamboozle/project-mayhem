export interface InitialEventFilters {
  [key: string]: string | string[] | undefined;
  all?: string;
  categories?: string[];
  creator?: string;
  startTime?: string;
  startTimeTo?: string;
}