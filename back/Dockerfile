# Use Maven image for building and running in dev mode
FROM maven:3.8.8-eclipse-temurin-17 AS build

# Set working directory
WORKDIR /app

# Copy the project files to the container
COPY . .

# Expose the default Quarkus dev mode port
EXPOSE 8080
EXPOSE 5005

# Command to run Quarkus in dev mode
CMD ["mvn", "quarkus:dev"]
