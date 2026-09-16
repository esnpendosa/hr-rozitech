'use client';

import React from 'react';
import Link from 'next/link';

export interface RmihLogoProps {
  size?: 'sm' | 'md' | 'lg' | 'xl';
  variant?: 'full' | 'emblem';
  href?: string;
  className?: string;
  subColor?: string;
  theme?: 'auto' | 'dark' | 'light';
  forceDark?: boolean;
}

/**
 * Official RMIH Emblem Icon (People + Swoosh + Analytics Growth + Gear)
 */
export function RmihEmblem({
  className = 'w-9 h-9',
}: {
  className?: string;
}) {
  return (
    <div className={`relative inline-flex items-center justify-center ${className}`}>
      <img
        src="/brand/rmih-emblem.png"
        alt="RMIH Emblem"
        className="w-full h-full object-contain"
        loading="eager"
      />
    </div>
  );
}

export function RmihLogoIcon({
  size = 'md',
  className = '',
}: {
  size?: 'sm' | 'md' | 'lg' | 'xl';
  className?: string;
}) {
  const sizeClasses = {
    sm: 'w-7 h-7',
    md: 'w-9 h-9',
    lg: 'w-11 h-11',
    xl: 'w-14 h-14',
  }[size];

  return <RmihEmblem className={`${sizeClasses} ${className}`} />;
}

/**
 * Official RMIH Logo Component
 * Uses the official logo with light and dark mode adaptive rendering.
 */
export function RmihLogo({
  size = 'md',
  variant = 'full',
  href = '/',
  className = '',
  theme = 'auto',
  forceDark = false,
}: RmihLogoProps) {
  const heightClasses = {
    sm: 'h-7 sm:h-8',
    md: 'h-9 sm:h-10',
    lg: 'h-12',
    xl: 'h-14 sm:h-16',
  }[size];

  const isDarkForced = forceDark || theme === 'dark';
  const isLightForced = theme === 'light';

  const content = variant === 'emblem' ? (
    <RmihLogoIcon size={size} className={className} />
  ) : (
    <div className={`inline-flex items-center ${heightClasses} ${className}`}>
      {isDarkForced ? (
        /* Explicit dark theme */
        <img
          src="/brand/rmih-logo-dark.png"
          alt="RMIH - Resources Management Integrated Human"
          className={`${heightClasses} w-auto object-contain`}
          loading="eager"
        />
      ) : isLightForced ? (
        /* Explicit light theme */
        <img
          src="/brand/rmih-logo.png"
          alt="RMIH - Resources Management Integrated Human"
          className={`${heightClasses} w-auto object-contain`}
          loading="eager"
        />
      ) : (
        /* Auto adaptive */
        <>
          <img
            src="/brand/rmih-logo.png"
            alt="RMIH - Resources Management Integrated Human"
            className={`${heightClasses} w-auto object-contain dark:hidden`}
            loading="eager"
          />
          <img
            src="/brand/rmih-logo-dark.png"
            alt="RMIH - Resources Management Integrated Human"
            className={`${heightClasses} w-auto object-contain hidden dark:block`}
            loading="eager"
          />
        </>
      )}
    </div>
  );

  if (href) {
    return (
      <Link href={href} className="inline-flex items-center focus:outline-none transition-opacity hover:opacity-95">
        {content}
      </Link>
    );
  }

  return content;
}

export default RmihLogo;
