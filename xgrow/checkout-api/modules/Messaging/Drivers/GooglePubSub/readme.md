# Running Google Pub/Sub locally (develop)

Run the Pub/Sub emulator via docker:

```
docker run -it --rm -p "8085:8085" google/cloud-sdk gcloud beta emulators pubsub start --host-port=0.0.0.0:8085
```

- The ENV must be set to `PUBSUB_EMULATOR_HOST=localhost:8085`
- `GOOGLE_APPLICATION_CREDENTIALS` must be commented   

