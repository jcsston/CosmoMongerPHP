#!/bin/sh

## resolve links - $0 may be a symlink
PRG="$0"
while [ -h "$PRG" ] ; do
  ls=`ls -ld "$PRG"`
  link=`expr "$ls" : '.*-> \(.*\)$'`
  if expr "$link" : '/.*' > /dev/null; then
  PRG="$link"
  else
  PRG=`dirname "$PRG"`"/$link"
  fi
done

LIQUIBASE_HOME=`dirname "$PRG"`

# make it fully qualified
LIQUIBASE_HOME=`cd "$LIQUIBASE_HOME" && pwd`

# build classpath from all jars in lib
CP=.
for i in "$LIQUIBASE_HOME"/liquibase*.jar; do
  CP="$CP":"$i"
done
for i in "$LIQUIBASE_HOME"/lib/*.jar; do
  CP="$CP":"$i"
done

# add any JVM options here
JAVA_OPTS=

java -cp $CP $JAVA_OPTS liquibase.commandline.Main $@
