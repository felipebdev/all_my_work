import { FhirCustomBundle } from '../../fhir/types';

interface UserDevice {
  id: string;
  verified: boolean;
}

interface UserResource {
  resourceType: 'User';
  'status-cognito'?:
    | 'UNCONFIRMED'
    | 'CONFIRMED'
    | 'ARCHIVED'
    | 'COMPROMISED'
    | 'UNKNOWN'
    | 'RESET_REQUIRED'
    | 'FORCE_CHANGE_PASSWORD';
  name: string;
  username: string;
  email: string;
  phone_number: string;
  device?: UserDevice[];
  picture?: string;
}

export type UserBundle = FhirCustomBundle<UserResource>;
