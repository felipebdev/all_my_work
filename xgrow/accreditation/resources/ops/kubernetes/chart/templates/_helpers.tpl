{{/* vim: set filetype=mustache: */}}
{{/*
Expand the name of the chart.
*/}}
{{- define "integration.name" -}}
{{- default .Chart.Name .Values.nameOverride | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{/*
Create a default fully qualified app name.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
If release name contains chart name it will be used as a full name.
*/}}
{{- define "integration.fullname" -}}
{{- if .Values.fullnameOverride -}}
{{- .Values.fullnameOverride | trunc 63 | trimSuffix "-" -}}
{{- else -}}
{{- $name := default .Chart.Name .Values.nameOverride -}}
{{- if contains $name .Release.Name -}}
{{- .Release.Name | trunc 63 | trimSuffix "-" -}}
{{- else -}}
{{- printf "%s-%s" .Release.Name $name | trunc 63 | trimSuffix "-" -}}
{{- end -}}
{{- end -}}
{{- end -}}

{{/*
Create chart name and version as used by the chart label.
*/}}
{{- define "integration.chart" -}}
{{- printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{/*
Add extra labels from values and global
*/}}
{{- define "integration.extraLabels" -}}
{{- if .Values.extraLabels }}
{{ toYaml .Values.extraLabels | indent 4 }}
{{- end }}
{{- if .Values.global }}
{{ toYaml .Values.global.extraLabels | indent 4 }}
{{- end }}
{{- end -}}


{{/*
Add default chart labels
*/}}
{{- define "integration.defaultLabels" -}}
    app: {{ template "integration.name" . }}
    chart: {{ include "integration.chart" . }}
    release: {{ .Release.Name }}
    heritage: {{ .Release.Service }}
{{- end -}}
