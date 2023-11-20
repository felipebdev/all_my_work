import { Bundle, BundleEntry } from 'fhir/r4';

interface CustomBundleEntry<T = any> extends Omit<BundleEntry, 'resource'> {
  resource: T;
}

export interface FhirCustomBundle<T = any> extends Omit<Bundle, 'entry'> {
  entry: CustomBundleEntry<T>[];
}
