type UserStatus =
  | 'UNCONFIRMED'
  | 'CONFIRMED'
  | 'ARCHIVED'
  | 'COMPROMISED'
  | 'UNKNOWN'
  | 'RESET_REQUIRED'
  | 'FORCE_CHANGE_PASSWORD';

interface CognitoUserAttribute {
  Name: string;
  Value: string;
}

export interface CognitoUser {
  Username: string;
  UserAttributes: CognitoUserAttribute[];
  Enabled: boolean;
  UserCreateDate: Date;
  UserLastModifiedDate: Date;
  UserStatus: UserStatus;
}
