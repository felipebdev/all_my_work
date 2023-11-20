import { appConfig, loggerConfig, throttleConfig, cacheConfig } from '@app/common/configs'
import { HealthController } from '@app/common/controllers'
import { loggerFactory } from '@app/common/factories/logger.factory'
import { CommonModuleOptions } from '@app/common/interfaces'
import { CorrelationIdMiddleware } from '@app/common/middlewares'
import { AppConfigService, HealthService, StrategyExplorerService, ContextService } from '@app/common/services'
import {
  ActiveHandlesMetric,
  ActiveHandlesTotalMetric,
  ControllerInjector,
  EventEmitterInjector,
  GuardInjector,
  HttpRequestDurationSeconds,
  LoggerInjector,
  OpenTelemetryModule,
  PipeInjector,
  ProcessStartTimeMetric,
  ResourceMetric,
  ScheduleInjector,
  TraceService
} from '@metinseylan/nestjs-opentelemetry'
import { HttpModule } from '@nestjs/axios'
import { DynamicModule, Module, CacheModule } from '@nestjs/common'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { TerminusModule } from '@nestjs/terminus'
import { ThrottlerModule, ThrottlerModuleOptions } from '@nestjs/throttler'
import { AsyncLocalStorageContextManager } from '@opentelemetry/context-async-hooks'
import { CompositePropagator, W3CBaggagePropagator, W3CTraceContextPropagator } from '@opentelemetry/core'
import { JaegerExporter } from '@opentelemetry/exporter-jaeger'
import { PrometheusExporter } from '@opentelemetry/exporter-prometheus'
import { AwsInstrumentation } from '@opentelemetry/instrumentation-aws-sdk'
import { ConnectInstrumentation } from '@opentelemetry/instrumentation-connect'
import { DnsInstrumentation } from '@opentelemetry/instrumentation-dns'
import { ExpressInstrumentation } from '@opentelemetry/instrumentation-express'
import { GenericPoolInstrumentation } from '@opentelemetry/instrumentation-generic-pool'
import { HttpInstrumentation } from '@opentelemetry/instrumentation-http'
import { IORedisInstrumentation } from '@opentelemetry/instrumentation-ioredis'
import { NestInstrumentation } from '@opentelemetry/instrumentation-nestjs-core'
import { NetInstrumentation } from '@opentelemetry/instrumentation-net'
import { PinoInstrumentation } from '@opentelemetry/instrumentation-pino'
import { B3InjectEncoding, B3Propagator } from '@opentelemetry/propagator-b3'
import { JaegerPropagator } from '@opentelemetry/propagator-jaeger'
import { BatchSpanProcessor } from '@opentelemetry/sdk-trace-base'
import { AcceptLanguageResolver, I18nModule, QueryResolver } from 'nestjs-i18n'
import { LoggerModule } from 'nestjs-pino'
import * as redisStore from 'cache-manager-redis-store'
import path from 'path'

@Module({})
export class CommonModule {
  static register(options: CommonModuleOptions): DynamicModule {
    return {
      module: CommonModule,
      imports: [
        ConfigModule.forRoot({ ...options.configModule }),
        ConfigModule.forFeature(appConfig()),
        ConfigModule.forFeature(throttleConfig()),
        ConfigModule.forFeature(loggerConfig()),
        ConfigModule.forFeature(cacheConfig()),
        HttpModule,
        TerminusModule,
        I18nModule.forRoot({
          fallbackLanguage: 'en',
          loaderOptions: {
            path: path.join(__dirname, '/../i18n/lang'),
            watch: true
          },
          resolvers: [{ use: QueryResolver, options: ['lang', 'locale'] }, AcceptLanguageResolver]
        }),
        ThrottlerModule.forRootAsync({
          inject: [ConfigService],
          useFactory: async (configService: ConfigService): Promise<ThrottlerModuleOptions> => {
            return {
              ttl: configService.get<number>('throttle.ttl'),
              limit: configService.get<number>('throttle.limit')
            }
          }
        }),
        OpenTelemetryModule.forRoot({
          applicationName: 'q-token-rules-node',
          traceAutoInjectors: [
            ControllerInjector,
            GuardInjector,
            EventEmitterInjector,
            ScheduleInjector,
            PipeInjector,
            LoggerInjector
          ],
          metricAutoObservers: [
            ResourceMetric,
            ProcessStartTimeMetric,
            ActiveHandlesMetric,
            ActiveHandlesTotalMetric,
            HttpRequestDurationSeconds
          ],
          spanProcessor: new BatchSpanProcessor(new JaegerExporter()),
          metricExporter: new PrometheusExporter({
            endpoint: 'metrics',
            port: 8081
          }),
          metricInterval: 1000,
          contextManager: new AsyncLocalStorageContextManager(),
          textMapPropagator: new CompositePropagator({
            propagators: [
              new JaegerPropagator(),
              new W3CTraceContextPropagator(),
              new W3CBaggagePropagator(),
              new B3Propagator(),
              new B3Propagator({
                injectEncoding: B3InjectEncoding.MULTI_HEADER
              })
            ]
          }),
          instrumentations: [
            new AwsInstrumentation(),
            new ConnectInstrumentation(),
            new DnsInstrumentation(),
            new ExpressInstrumentation(),
            new GenericPoolInstrumentation(),
            new HttpInstrumentation(),
            new IORedisInstrumentation(),
            new NestInstrumentation(),
            new NetInstrumentation(),
            new PinoInstrumentation()
          ]
        }),
        LoggerModule.forRootAsync({
          inject: [ConfigService, TraceService],
          useFactory: loggerFactory
        }),
        CacheModule.registerAsync({
          inject: [ConfigService],
          useFactory: async (configService: ConfigService) => {
            if (configService.get<string>('cache.store').toLowerCase() === 'redis') {
              return {
                store: redisStore,
                host: configService.get<string>('cache.host'),
                port: configService.get<string>('cache.port'),
                ttl: configService.get<number>('cache.ttl'),
                password: configService.get<string>('cache.password'),
                prefix: configService.get<string>('cache.prefix')
              }
            } else {
              return null
            }
          }
        })
      ],
      providers: [StrategyExplorerService, CorrelationIdMiddleware, HealthService, AppConfigService, ContextService],
      controllers: [HealthController],
      exports: [StrategyExplorerService, AppConfigService, ContextService]
    }
  }
}
