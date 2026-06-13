#!/bin/bash
# ═══════════════════════════════════════════════════════════
# BGame CI/CD — GitHub Actions Deploy Workflow
# يُنفَّذ تلقائياً عند كل push على main
# ═══════════════════════════════════════════════════════════

# Required GitHub Secrets (Settings → Secrets → Actions):
#   SERVER_HOST     : IP address of your server
#   SERVER_USER     : SSH username (e.g. root or ubuntu)
#   SERVER_SSH_KEY  : Private SSH key content
#   SERVER_PORT     : SSH port (default 22)
#   DEPLOY_PATH     : Path on server (e.g. /opt/bgame)
